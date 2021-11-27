<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class Chapter extends Model {
    protected $fillable = [
        'comic_id', 'team_id', 'team2_id', 'volume', 'chapter', 'subchapter', 'title', 'slug', 'salt', 'prefix',
        'hidden', 'licensed', 'views', 'rating', 'download_link', 'thumbnail', 'language', 'published_on',
        'publish_start', 'publish_end'
    ];

    protected $casts = [
        'id' => 'integer',
        'comic_id' => 'integer',
        'team_id' => 'integer',
        'team2_id' => 'integer',
        'volume' => 'integer',
        'chapter' => 'integer',
        'subchapter' => 'integer',
        'hidden' => 'integer',
        'licensed' => 'integer',
        'views' => 'integer',
        'rating' => 'decimal:2',
        'published_on' => 'datetime',
        'publish_start' => 'datetime',
        'publish_end' => 'datetime',
    ];

    public function scopePublished($query) {
        $now = Carbon::now();
        return $query->where('hidden', 0)->where('publish_start', '<', $now)->where(
            function ($q) use ($now) {
                $q->where('publish_end', '>', $now)->orWhereNull('publish_end');
            }
        );
    }

    public function scopePublic($query) {
        if (!Auth::check() || !Auth::user()->hasPermission('checker'))
            return $query->published();
        else if (Auth::user()->hasPermission('manager') || Auth::user()->all_comics)
            return $query;
        else {
            $comics = Auth::user()->comics()->select('comic_id');
            return $query->where(function ($q) use ($comics) {
                $q->published()->orWhereIn('comic_id', $comics);
            });
        }
    }

    public function pages() {
        return $this->hasMany(Page::class)->orderBy('filename', 'asc')->orderBy('id', 'asc');
    }

    public function views_list() {
        return $this->hasMany(View::class);
    }

    public function ratings() {
        return $this->hasMany(Rating::class);
    }

    /*public function publicPages() {
        return $this->pages()->where('hidden', 0);
    }*/

    public function comic() {
        return $this->belongsTo(Comic::class);
    }

    public function teams() {
        return $this->belongsTo(Team::class);
    }

    public function download() {
        return $this->hasOne(ChapterDownload::class);
    }

    public function pdf() {
        return $this->hasOne(ChapterPdf::class);
    }

    public static function volume_download($chapter) {
        return VolumeDownload::where([
            ['comic_id', $chapter->comic_id],
            ['language', $chapter->language],
            ['volume', $chapter->volume]
        ])->first();
    }

    public static function slug($comic_id, $slug) {
        return Chapter::where([['slug', $slug], ['comic_id', $comic_id]])->first();
    }

    public static function publicFilterByCh($comic, $ch) {
        return $comic->publicChapters()->where([
            ['language', $ch['lang']],
            ['volume', $ch['vol']],
            ['chapter', $ch['ch']],
            ['subchapter', $ch['sub']],
        ])->first();
    }

    public static function isLicensed($chapter) {
        return $chapter->licensed && (!Auth::check() || !Auth::user()->canSee($chapter->comid_id));
    }

    public static function slugLangVolChSub($chapter) {
        $lang = $chapter->language ?: "N";
        $vol = $chapter->volume !== null ? $chapter->volume : "N";
        $ch = $chapter->chapter !== null ? $chapter->chapter : "N";
        $sub = $chapter->subchapter !== null ? $chapter->subchapter : "N";
        return $lang . '-' . $vol . '-' . $ch . '-' . $sub;
    }

    public static function buildPath($comic, $chapter) {
        return Comic::buildPath($comic) . '/' . Chapter::slugLangVolChSub($chapter) . '-' . $chapter->slug
            . '_' . $chapter->salt;
    }

    public static function path($comic, $chapter) {
        return 'public/' . Chapter::buildPath($comic, $chapter);
    }

    public static function absolutePath($comic, $chapter) {
        return public_path() . '/storage/' . Chapter::buildPath($comic, $chapter);
    }

    public static function storagePath($comic, $chapter) {
        return storage_path("app/" . Chapter::path($comic, $chapter));
    }

    public static function buildUri($comic, $chapter) {
        $url = "/$comic->slug/" . $chapter['language'];
        if (isset($chapter['volume']) && $chapter['volume'] !== null) $url .= '/vol/' . $chapter['volume'];
        if (isset($chapter['chapter']) && $chapter['chapter'] !== null) $url .= '/ch/' . $chapter['chapter'];
        if (isset($chapter['subchapter']) && $chapter['subchapter'] !== null) $url .= '/sub/' . $chapter['subchapter'];
        return $url;
    }

    public static function getUrl($comic, $chapter) {
        return "/read" . Chapter::buildUri($comic, $chapter);
    }

    public static function getChapterPdf($comic, $chapter) {
        if (Chapter::canChapterPdf($chapter)) {
            return "/pdf" . Chapter::buildUri($comic, $chapter);
        }
        return null;
    }

    public static function getChapterDownload($comic, $chapter) {
        if (Chapter::canChapterDownload($chapter)) {
            return "/download" . Chapter::buildUri($comic, $chapter);
        }
        return null;
    }

    public static function getVolumeDownload($comic, $chapter) {
        if (Chapter::canVolumeDownload($comic)) {
            $ch = ['language' => $chapter->language, 'volume' => $chapter->volume];
            return "/download" . Chapter::buildUri($comic, $ch);
        }
        return null;
    }

    public static function getThumbnailUrl($comic, $chapter) {
        if ($chapter->thumbnail) return $chapter->thumbnail;
        if (!config('settings.default_chapter_thumbnail')) return Comic::getThumbnailUrl($comic);
        $thumbnail_path = 'storage/' . Chapter::buildPath($comic, $chapter) . '/' . $chapter->pages()->first()->filename;
        return File::exists($thumbnail_path) ? asset($thumbnail_path) : null;
    }

    public static function canChapterPdf($chapter) {
        return class_exists('Imagick') && ((config('settings.pdf_chapter') && !$chapter->licensed) || (Auth::check() && Auth::user()->canSee($chapter->comic_id)));
    }

    public static function canChapterDownload($chapter) {
        return (config('settings.download_chapter') && !$chapter->licensed) || (Auth::check() && Auth::user()->canSee($chapter->comic_id));
    }

    public static function canVolumeDownload($comic) {
        return config('settings.download_volume') || (Auth::check() && Auth::user()->canSee($comic->id));
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
                } elseif (strlen($v) > 3 && $v[4] === ':') {
                    $pre = substr($v, 0, 4);
                    $past = substr($v, 5, -1);
                    if (($pre === '{vol' && $chapter->volume !== null) ||
                        ($pre === '{num' && $chapter->chapter !== null) ||
                        ($pre === '{sub' && $chapter->subchapter !== null) ||
                        ($pre === '{tit' && $chapter->title !== null)) {
                        $name .= $past;
                    }
                } elseif ($v[0] !== '{' && substr($v, -1) !== '}') {
                    $name .= $v;
                }
            }
        }

        if (!preg_match("/[A-z0-9]+/", $name)) {
            $name = Chapter::getVolChSub($chapter);
            if ($name !== "" && $chapter->title !== null) $name .= " - ";
            if ($chapter->title !== null) $name .= "$chapter->title";
        }
        if ($name === "") $name = 'Oneshot';
        if ($chapter->prefix !== null) $name = "$chapter->prefix " . $name;
        return $name;
    }

    public static function getVolChSub($chapter) {
        $name = "";
        if ($chapter->volume !== null) $name .= "Vol.$chapter->volume ";
        if ($chapter->chapter !== null) $name .= "Ch.$chapter->chapter";
        if ($chapter->subchapter !== null) $name .= ".$chapter->subchapter";
        return $name;
    }

    public static function getFormFields() {
        $teams = Team::all();
        $id_teams = $teams->pluck('id');
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
                    'pattern' => '[1-9]\d*|0',
                ],
                'values' => ['integer', 'min:0'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'chapter',
                    'label' => 'Chapter',
                    'hint' => 'Insert chapter\'s number',
                    'pattern' => '[1-9]\d*|0',
                ],
                'values' => ['integer', 'min:0'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'subchapter',
                    'label' => 'Subchapter',
                    'hint' => 'Insert the number of a intermediary chapter. Remember "0" is showed too, if you don\'t need a subchapter keep this field empty [Example: inserting chapter "2" and subchapter "3" the showed chapter is "2.3"]',
                    'pattern' => '[1-9]\d*|0',
                ],
                'values' => ['integer', 'min:0'],
            ], [
                'type' => 'input_checkbox',
                'parameters' => [
                    'field' => 'hidden',
                    'label' => 'Hidden',
                    'hint' => 'Check to hide this chapter',
                    'checked' => intval(config('settings.default_hidden_chapter')),
                    'required' => 1,
                ],
                'values' => ['boolean'],
            ], [
                'type' => 'input_checkbox',
                'parameters' => [
                    'field' => 'licensed',
                    'label' => 'Licensed',
                    'hint' => 'Check to mark this chapter as licensed (it will be not accessible)',
                    'checked' => 0,
                    'required' => 1,
                ],
                'values' => ['boolean'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'prefix',
                    'label' => 'Prefix',
                    'hint' => 'If you want to a prefix to this specific chapter. If you want the same prefix for every chapter use "Custom chapter" of Comic [Example: "[Deluxe]", "[IT]", etc.]',
                    'disabled' => true,
                    'edit' => true,
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_datetime_local',
                'parameters' => [
                    'field' => 'published_on',
                    'label' => 'Published on',
                    'hint' => 'It won\'t be used to programs the publication but only for information purpose. If your browser (es. Firefox) doesn\'t show a data picker, please use as format yyyy-mm-ddTHH:MM [Example: 2020-09-10T19:34]',
                    'required' => 1,
                ],
                'values' => ['regex:/^[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}$/'],
            ], [
                'type' => 'input_datetime_local',
                'parameters' => [
                    'field' => 'publish_start',
                    'label' => 'Publish start',
                    'hint' => 'It is used to programs the publication. If your browser (es. Firefox) doesn\'t show a data picker, please use as format yyyy-mm-ddTHH:MM [Example: 2020-09-10T19:34]',
                    'required' => 1,
                ],
                'values' => ['regex:/^[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}$/'],
            ], [
                'type' => 'input_datetime_local',
                'parameters' => [
                    'field' => 'publish_end',
                    'label' => 'Publish end',
                    'hint' => 'It is used to programs the publication. If your browser (es. Firefox) doesn\'t show a data picker, please use as format yyyy-mm-ddTHH:MM [Example: 2020-09-10T19:34]',
                    'clear' => 1,
                ],
                'values' => ['regex:/^[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}$/'],
            ], [
                'type' => 'input_hidden',
                'parameters' => [
                    'field' => 'timezone',
                    'required' => 1,
                    'default' => 'UTC',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'views',
                    'label' => 'Views',
                    'hint' => 'The number of views of this chapter. This field is meant to be used when you want to recreate a chapter without starting the views from 0',
                    'disabled' => true,
                    'edit' => true,
                    'pattern' => '[1-9]\d*|0',
                ],
                'values' => ['integer', 'min:0'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'download_link',
                    'label' => 'Download link',
                    'hint' => 'If you want to use a external download link use this field, else a zip is automatically generated (if is enabled in the options)',
                    'disabled' => true,
                    'edit' => true,
                    'max' => 512,
                ],
                'values' => ['max:512'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'thumbnail',
                    'label' => 'Thumbnail link',
                    'hint' => 'Link of the thumbnail used in the socials. If empty, the first page of the chapter will be used',
                    'disabled' => true,
                    'edit' => true,
                    'max' => 512,
                ],
                'values' => ['max:512'],
            ], [
                'type' => 'select',
                'parameters' => [
                    'field' => 'language',
                    'label' => 'Language',
                    'hint' => 'Select the language of this chapter',
                    'options' => ['en', 'es', 'fr', 'it', 'pt', 'jp',],
                    'selected' => config('settings.default_language'),
                    'required' => 1,
                ],
                'values' => ['string', 'size:2'],
            ], [
                'type' => 'select',
                'parameters' => [
                    'field' => 'team_id',
                    'label' => 'Team 1',
                    'hint' => 'Select the team who worked to this chapter',
                    'options' => $teams,
                    'required' => 1,
                ],
                'values' => ['integer', Rule::in($id_teams)],
            ], [
                'type' => 'select',
                'parameters' => [
                    'field' => 'team2_id',
                    'label' => 'Team 2',
                    'hint' => 'Select a second (optional) team who worked to this chapter',
                    'options' => $teams,
                    'nullable' => 'nullable',
                ],
                'values' => ['integer', Rule::in($id_teams) . ',"0"'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'slug',
                    'label' => 'URL slug',
                    'hint' => 'Automatically generated, use this if you want to have a custom URL slug',
                    'disabled' => true,
                    'edit' => true,
                    'max' => '48',
                ],
                'values' => ['max:48'],
            ],

        ];

    }

    public static function generateReaderArray($comic, $chapter) {
        $now = Carbon::now();
        if (!$comic || !$chapter || $comic->id !== $chapter->comic_id) return null;
        return [
            'id' => Auth::check() && Auth::user()->canEdit($comic->id) ? $chapter->id : null,
            'full_title' => Chapter::name($comic, $chapter),
            'title' => $chapter->title,
            'volume' => $chapter->volume,
            'chapter' => $chapter->chapter,
            'subchapter' => $chapter->subchapter,
            'full_chapter' => "[" . strtoupper($chapter->language) . "] " . Chapter::getVolChSub($chapter),
            'views' => $chapter->views ?: 0,
            'rating' => $chapter->rating,
            'download_link' => $chapter->download_link,
            'language' => $chapter->language,
            'teams' => [Team::generateReaderArray(Team::find($chapter->team_id)),
                Team::generateReaderArray(Team::find($chapter->team2_id)),],
            'updated_at' => $chapter->updated_at,
            'published_on' => $chapter->published_on,
            'hidden' => ($chapter['hidden'] || $chapter->publish_start > $now ||
                ($chapter->publish_end && $chapter->publish_end < $now))? 1 : 0, // "->hidden" is the eloquent variable for hidden attributes
            'licensed' => $chapter->licensed,
            'slug_lang_vol_ch_sub' => Chapter::slugLangVolChSub($chapter),
            'url' => Chapter::getUrl($comic, $chapter),
            'chapter_download' => Chapter::getChapterDownload($comic, $chapter),
            'volume_download' => Chapter::getVolumeDownload($comic, $chapter),
            'pdf' => Chapter::getChapterPdf($comic, $chapter),
        ];
    }

    public static function getFormFieldsForValidation() {
        return getFormFieldsForValidation(Chapter::getFormFields());
    }

    public static function getFieldsFromRequest($request, $comic, $form_fields) {
        $fields = getFieldsFromRequest($request, $form_fields);
        $fields['published_on'] = convertToUTC($fields['published_on'], $fields['timezone']);
        $fields['publish_start'] = convertToUTC($fields['publish_start'], $fields['timezone']);
        if($fields['publish_end']) $fields['publish_end'] = convertToUTC($fields['publish_end'], $fields['timezone']);
        Auth::user()->update(['timezone' => $fields['timezone']]);
        unset($fields['timezone']);
        $fields['comic_id'] = $comic->id;
        if ($fields['team2_id'] === '0') unset($fields['team2_id']);
        return $fields;
    }

    public static function getFieldsIfValid($comic, $request) {
        $form_fields = Chapter::getFormFieldsForValidation();
        $request->validate($form_fields);
        $fields = Chapter::getFieldsFromRequest($request, $comic, $form_fields);
        $duplicated_chapter = Chapter::where([
            ['id', '<>', $request->route('chapter')],
            ['comic_id', $comic->id],
            ['volume', $fields['volume']],
            ['chapter', $fields['chapter']],
            ['subchapter', $fields['subchapter']],
            ['language', $fields['language']],
        ])->first();
        if ($duplicated_chapter) throw new \DuplicatedChapter('Chapter duplicated, there is already a chapter for this comic with this combination of language, volume, chapter and subchapter.');
        return $fields;
    }

    public static function generateSlug($fields) {
        return generateSlug(new Chapter, $fields);
    }
}
