<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comic extends Model {
    protected $fillable = [
        'name', 'slug', 'salt', 'hidden', 'author', 'artist', 'target', 'status', 'description', 'thumbnail',
        'custom_chapter', 'comic_format_id', 'adult',
    ];

    public function format() {
        return $this->belongsTo(ComicFormat::class);
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function chapters() {
        return $this->hasMany(Chapter::class);
    }

    public static function slug($slug) {
        return Comic::where('slug', $slug)->first();
    }

    public static function buildPath($comic) {
        return 'comics/' . $comic->slug . '_' . $comic->salt;
    }

    public static function path($comic_id) {
        return 'public/' . Comic::buildPath(Comic::find($comic_id));
    }

    public static function getThumbnailUrl($comic_id) {
        $comic = Comic::find($comic_id);
        return asset('storage/' . Comic::buildPath($comic) . '/' . $comic->thumbnail);
    }

    public static function getFormFields() {
        $comic_formats = ComicFormat::all();
        return [
            [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'name',
                    'label' => 'Name',
                    'hint' => 'Insert comic\'s name',
                    'required' => 'required',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'slug',
                    'label' => 'URL slug',
                    'hint' => 'Automatically generated, use this if you want to have a custom URL slug',
                ],
                'values' => ['max:191'],
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
                    'field' => 'author',
                    'label' => 'Author',
                    'hint' => 'Insert comic\'s author',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'artist',
                    'label' => 'Artist',
                    'hint' => 'Insert comic\'s artist',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'target',
                    'label' => 'Target',
                    'hint' => 'Insert comic\'s target [Example: Shonen, Seinen, Shojo, Kodomo, Josei]',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'status',
                    'label' => 'Status',
                    'hint' => 'Insert comic\'s status [Example: Airing, Finished]',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'textarea',
                'parameters' => [
                    'field' => 'description',
                    'label' => 'Description',
                    'hint' => 'Insert comic\'s description',
                ],
                'values' => ['max:3000'],
            ], [
                'type' => 'input_file',
                'parameters' => [
                    'field' => 'thumbnail',
                    'label' => 'Thumbnail',
                    'hint' => 'Insert comic\'s thumbnail',
                ],
                'values' => ['file', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
            ], [
                'type' => 'select',
                'parameters' => [
                    'field' => 'comic_format_id',
                    'label' => 'Format',
                    'hint' => 'Select comic\'s format',
                    'options' => $comic_formats,
                ],
                'values' => ['integer', 'between:1,' . $comic_formats->count()],
            ], [
                'type' => 'input_checkbox',
                'parameters' => [
                    'field' => 'adult',
                    'label' => 'Adult',
                    'hint' => 'Check to set this comic for adults only',
                ],
                'values' => ['boolean'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'custom_chapter',
                    'label' => 'Custom chapter',
                    'hint' => 'Replace the default chapter with a custom format [Example: "{num}{sub}{ord} punch" returns "2nd punch"]',
                ],
                'values' => ['max:191'],
            ],

        ];

    }

    public static function getFormFieldsForValidation() {
        return getFormFieldsForValidation(Comic::getFormFields());
    }

    public static function generateSlug($fields) {
        return generateSlug(new Comic, $fields);
    }
}
