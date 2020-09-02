<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model {
    protected $fillable = [
        'comic_id', 'team_id', 'team2_id', 'volume', 'chapter', 'subchapter', 'title', 'slug', 'salt', 'hidden',
        'views', 'download_link',
    ];

    public function pages() {
        return $this->hasMany(Page::class);
    }

    public function comic() {
        return $this->belongsTo(Comic::class);
    }

    public function teams() {
        return $this->belongsTo(Team::class);
    }

    public static function slug($slug) {
        return Chapter::where('slug', $slug)->first();
    }

    public static function buildPath($comic, $chapter) {
        return Comic::buildPath($comic) . '/' . $chapter->slug . '_' . $chapter->salt;
    }

    public static function path($chapter_id) {
        $chapter = Chapter::find($chapter_id);
        return 'public/' . Chapter::buildPath(Comic::find($chapter->comic_id), $chapter);
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
                    'field' => 'views',
                    'label' => 'Views',
                    'hint' => 'The number of views of this chapter. This field is meant to be used when you want to recreate a chapter without starting the views from 0.',
                ],
                'values' => ['integer', 'min:0'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'download_link',
                    'label' => 'Download link',
                    'hint' => 'If you want to use a external download link use this field, else a zip is automatically generated (if is enabled in the options)',
                ],
                'values' => ['max:191'],
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
                ],
                'values' => ['max:191'],
            ],

        ];

    }

    public static function getFormFieldsForValidation() {
        return getFormFieldsForValidation(Chapter::getFormFields());
    }

    public static function generateSlug($fields) {
        return generateSlug(new Chapter, $fields);
    }
}
