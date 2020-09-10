<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Comic extends Model {
    protected $fillable = [
        'name', 'slug', 'salt', 'hidden', 'author', 'artist', 'target', 'genres', 'status', 'description', 'thumbnail',
        'custom_chapter', 'comic_format_id', 'adult',
    ];

    public function scopePublic() {
        return $this->where('hidden', 0);
    }

    public function format() {
        return $this->belongsTo(ComicFormat::class);
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function chapters() {
        return $this->hasMany(Chapter::class)
            ->orderByDesc('volume')
            ->orderByDesc('chapter')
            ->orderByDesc('subchapter')
            ->orderByDesc('title')
            ->orderByDesc('id');
    }

    public function publicChapters() {
        return $this->chapters()->where('hidden', 0);
    }

    public static function slug($slug) {
        return Comic::where('slug', $slug)->first();
    }

    public static function publicSlug($slug) {
        return Comic::public()->where('slug', $slug)->first();
    }

    public static function publicSearch($search) {
        $name = preg_replace("/[^A-Za-z0-9 ]/", '', $search);
        return Comic::public()->where('name', 'LIKE', '%' . $name . '%')->get();
    }

    public static function buildPath($comic) {
        return 'comics/' . $comic->slug . '_' . $comic->salt;
    }

    public static function path($comic) {
        return 'public/' . Comic::buildPath($comic);
    }

    public static function getThumbnailUrl($comic) {
        if ($comic->thumbnail === null || $comic->thumbnail === '') return null;
        return asset('storage/' . Comic::buildPath($comic) . '/' . $comic->thumbnail);
    }

    public static function getUrl($comic) {
        return "/comics/$comic->slug";
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
                    'disabled' => 'disabled',
                    'max' => '48',
                ],
                'values' => ['max:48'],
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
                    'hint' => 'Insert comic\'s target [Example: "Shonen", "Seinen", "Shojo", "Kodomo", "Josei"]',
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'genres',
                    'label' => 'Genres',
                    'hint' => 'Insert comic\'s genres separated by comma [Example: "Slice of life, Romance, Drama"]',
                ],
                'values' => ['max:500'],
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
                    'hint' => 'Replace the default chapter with a custom format. Syntax is: "{something:mystring}" which shows "mystring" only if you setted "something" in the chapter, while {something} shows the value of "something". Accepted values for "something": {vol}, {num}, {sub}, {tit}. Accepted characters in "mystring": everything (a single space too!) except "{" and "}". You can use {ord} after {something} to make it ordinal. [Example: "{vol:Vol.}{vol}{vol: }{num}{ord} punch {sub:Part }{sub}{tit: - }{tit}" returns "Vol.1 2nd punch Part 2 - NiceTitle" if everything is setted, while if a chapter has no Volume or Subchapter it will returns "2nd punch - NiceTitle"]',
                    'pattern' => '.*\{(vol|num|tit|sub)\}.*',
                ],
                'values' => ['max:191', 'regex:/^.*{(vol|num|tit|sub)}.*$/'],
            ],

        ];

    }

    public static function generateReaderArray($comic) {
        if (!$comic) return null;
        $genres = [];
        foreach (explode(',', $comic->genres) as $genre) {
            array_push($genres, ["name" => $genre, "slug" => Str::slug($genre)]);
        }
        return [
            'title' => $comic->name,
            'thumbnail' => Comic::getThumbnailUrl($comic) ?: config('app.url') . 'public/img/noimg.jpg', // TODO setting api
            'description' => $comic->description,
            'author' => $comic->author,
            'artist' => $comic->artist,
            'target' => $comic->target,
            'genres' => $genres,
            'status' => $comic->status,
            'format_id' => $comic->comic_format_id,
            'adult' => $comic->adult,
            'created_at' => $comic->created_at,
            'updated_at' => $comic->updated_at,
            'views' => Chapter::public()->where('comic_id', $comic->id)->sum('views'),
            'rating' => 6.91, // TODO rating
            'url' => Comic::getUrl($comic),
            'last_chapter' => Chapter::generateReaderArray($comic, Chapter::public()->where('comic_id', $comic->id)->orderByDesc('created_at')->first()),
        ];
    }

    public static function generateReaderArrayWithChapters($comic) {
        if(!$comic) return null;
        $response = Comic::generateReaderArray($comic);
        $response['chapters'] = [];
        $chapters = $comic->publicChapters;
        foreach ($chapters as $chapter) {
            array_push($response['chapters'], Chapter::generateReaderArray($comic, $chapter));
        }
        return $response;
    }

    public static function getFormFieldsForValidation() {
        return getFormFieldsForValidation(Comic::getFormFields());
    }

    public static function generateSlug($fields) {
        return generateSlug(new Comic, $fields);
    }
}
