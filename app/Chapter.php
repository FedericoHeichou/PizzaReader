<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model {
    protected $fillable = [
        'comic_id', 'team_id', 'team2_id', 'volume', 'chapter', 'subchapter', 'title', 'slug', 'salt', 'prefix',
        'hidden', 'views', 'download_link', 'language'
    ];

    public function scopePublic() {
        return $this->where('hidden', 0);
    }

    public function pages() {
        return $this->hasMany(Page::class)->orderBy('filename', 'asc')->orderBy('id', 'asc');
    }

    public function publicPages() {
        return $this->pages()->where('hidden', 0);
    }

    public function comic() {
        return $this->belongsTo(Comic::class);
    }

    public function teams() {
        return $this->belongsTo(Team::class);
    }

    public static function slug($comic_id, $slug) {
        return Chapter::where([['slug', $slug], ['comic_id', $comic_id]])->first();
    }

    public static function buildPath($comic, $chapter) {
        return Comic::buildPath($comic) . '/' . intval($chapter->volume) . '-' . intval($chapter->chapter) . '-' .
            intval($chapter->subchapter) . '-' . $chapter->slug . '_' . $chapter->salt;
    }

    public static function path($comic, $chapter) {
        return 'public/' . Chapter::buildPath($comic, $chapter);
    }

    public static function absolutePath($comic, $chapter) {
        return public_path() . '/storage/' . Chapter::buildPath($comic, $chapter);
    }

    public static function getUrl($comic, $chapter) {
        $url = Comic::getUrl($comic) . "/$chapter->language";
        if($chapter->volume !== null) $url .= "/vol/$chapter->volume";
        if($chapter->chapter !== null) $url .= "/ch/$chapter->chapter";
        if($chapter->subchapter !== null) $url .= "/sub/$chapter->subchapter";
        return $url;
    }

    public static function name($comic, $chapter) {
        $name = "";
        // Yandere-dev kicks in
        if ($comic->custom_chapter) {
            preg_match_all('/{[^{]*}|[^{|}]+/', $comic->custom_chapter, $matches);
            foreach ($matches[0] as $v) {
                if ($v === '{vol}') {
                    $name .= $chapter->volume;
                } elseif ($v === '{num}') {
                    $name .= $chapter->chapter;
                } elseif ($v === '{sub}') {
                    $name .= $chapter->subchapter;
                } elseif ($v === '{tit}') {
                    $name .= $chapter->title;
                } elseif ($v === '{ord}' && $name !== "") {
                    $num = substr($name, -1);
                    if (is_numeric($num)) {
                        if ($num === '1') $name .= 'st';
                        elseif ($num === '2') $name .= 'nd';
                        elseif ($num === '3') $name .= 'rd';
                        else $name .= 'th';
                    }
                } elseif (substr($v, 4, 1) === ':') {
                    $pre = substr($v, 0, 4);
                    $past = substr($v, 5, -1);
                    if (($pre === '{vol' && $chapter->volume !== null) ||
                        ($pre === '{num' && $chapter->chapter !== null) ||
                        ($pre === '{sub' && $chapter->subchapter !== null) ||
                        ($pre === '{tit' && $chapter->title !== null)) {
                        $name .= $past;
                    }
                } elseif (substr($v, 0, 1) !== '{' && substr($v, -1) !== '}') {
                    $name .= $v;
                }
            }
        }

        if (!preg_match("/[A-z0-9]+/", $name)) {
            if ($chapter->volume !== null) $name .= "Vol.$chapter->volume ";
            if ($chapter->chapter !== null) $name .= "Ch.$chapter->chapter";
            if ($chapter->subchapter !== null) $name .= ".$chapter->subchapter";
            if($name !== "" && $chapter->title !== null) $name .= " - ";
            if ($chapter->title !== null) $name .= "$chapter->title";
        }
        if ($name === "") $name = 'Oneshot';
        if ($chapter->prefix !== null) $name = "$chapter->prefix " . $name;
        return $name;
    }

    public static function getFormFields() {
        $teams = Team::all();
        return [
            [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'title',
                    'label' => 'Title',
                    'hint' => 'Insert chapter\'s title',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'volume',
                    'label' => 'Volume',
                    'hint' => 'Insert chapter\'s volume',
                ],
                'values' => ['integer', 'min:0'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'chapter',
                    'label' => 'Chapter',
                    'hint' => 'Insert chapter\'s number',
                ],
                'values' => ['integer', 'min:0'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'subchapter',
                    'label' => 'Subchapter',
                    'hint' => 'Insert the number of a intermediary chapter. Remember "0" is showed too, if you don\'t need a subchapter keep this field empty [Example: inserting chapter "2" and subchapter "3" the showed chapter is "2.3"]',
                ],
                'values' => ['integer', 'min:0'],
            ], [
                'type' => 'input_checkbox',
                'parameters' => [
                    'field' => 'hidden',
                    'label' => 'Hidden',
                    'hint' => 'Check to hide this comic',
                    'checked' => 'checked',
                ],
                'values' => ['boolean'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'prefix',
                    'label' => 'Prefix',
                    'hint' => 'If you want to a prefix to this specific chapter. If you want the same prefix for every chapter use "Custom chapter" of Comic [Example: "[Deluxe]", "[IT]", etc.]',
                    'disabled' => 'disabled',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'views',
                    'label' => 'Views',
                    'hint' => 'The number of views of this chapter. This field is meant to be used when you want to recreate a chapter without starting the views from 0',
                    'disabled' => 'disabled',
                ],
                'values' => ['integer', 'min:0'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'download_link',
                    'label' => 'Download link',
                    'hint' => 'If you want to use a external download link use this field, else a zip is automatically generated (if is enabled in the options)',
                    'disabled' => 'disabled',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'select',
                'parameters' => [
                    'field' => 'language',
                    'label' => 'Language',
                    'hint' => 'Select the language of this chapter',
                    'options' => ['en', 'es', 'fr', 'it', 'pt', 'jp',],
                    'selected' => 'it',
                    'required' => 'required',
                ],
                'values' => ['string', 'size:2'],
            ], [
                'type' => 'select',
                'parameters' => [
                    'field' => 'team_id',
                    'label' => 'Team 1',
                    'hint' => 'Select the team who worked to this chapter',
                    'options' => $teams,
                    'required' => 'required',
                ],
                'values' => ['integer', 'between:1,' . $teams->count()],
            ], [
                'type' => 'select',
                'parameters' => [
                    'field' => 'team2_id',
                    'label' => 'Team 2',
                    'hint' => 'Select a second (optional) team who worked to this chapter',
                    'options' => $teams,
                    'nullable' => 'nullable',
                ],
                'values' => ['integer', 'between:0,' . $teams->count()],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'slug',
                    'label' => 'URL slug',
                    'hint' => 'Automatically generated, use this if you want to have a custom URL slug',
                    'disabled' => 'disabled',
                ],
                'values' => ['max:191'],
            ],

        ];

    }

    public static function generateReaderArray($comic, $chapter) {
        if (!$comic || !$chapter || $comic->id !== $chapter->comic_id) return null;
        return [
            'full_title' => Chapter::name($comic, $chapter),
            'title' => $chapter->title,
            'volume' => $chapter->volume,
            'chapter' => $chapter->chapter,
            'subchapter' => $chapter->subchapter,
            'views' => $chapter->views ?: 0,
            'download_link' => $chapter->download_link,
            'language' => $chapter->language,
            'teams' => [Team::generateReaderArray(Team::find($chapter->team_id)),
                Team::generateReaderArray(Team::find($chapter->team2_id)),],
            'created_at' => $chapter->created_at,
            'updated_at' => $chapter->updated_at,
            'url' => Chapter::getUrl($comic, $chapter),
        ];
    }

    public static function getFormFieldsForValidation() {
        return getFormFieldsForValidation(Chapter::getFormFields());
    }

    public static function generateSlug($fields) {
        return generateSlug(new Chapter, $fields);
    }
}
