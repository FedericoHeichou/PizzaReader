<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Comic extends Model {
    protected $fillable = [
        'name', 'slug', 'salt', 'hidden', 'author', 'artist', 'target', 'genres', 'status', 'description', 'thumbnail',
        'custom_chapter', 'comic_format_id', 'adult', 'order_index'
    ];

    protected $casts = [
        'id' => 'integer',
        'hidden' => 'integer',
        'comic_format_id' => 'integer',
        'adult' => 'integer',
        'order_index' => 'float',
    ];

    public function scopePublic($query, $ord="order_index") {
        $query = $query->orderBy($ord);
        if ($ord !== 'name') $query = $query->orderBy('name');
        if (!Auth::check() || !Auth::user()->hasPermission('checker'))
            return $query->where('hidden', 0);
        else if (Auth::user()->hasPermission('manager'))
            return $query;
        else {
            $comics = Auth::user()->comics()->select('comic_id');
            return $query->where(function ($query) use ($comics) {
                $query->where('hidden', 0)->orWhereIn('id', $comics);
            });
        }
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
            ->orderByDesc('language');
    }

    public function volume_downloads() {
        return $this->hasMany(VolumeDownload::class);
    }

    public function publicChapters() {
        return $this->chapters()->public();
    }

    public static function slug($slug) {
        return Comic::where('slug', $slug)->first();
    }

    public static function publicSlug($slug) {
        return Comic::public()->where('slug', $slug)->first();
    }

    public function scopeSearch($query, $search, $column='name') {
        $comic_name = preg_replace("/[^A-Za-z0-9]/", '_', $search);
        if(preg_match("/[A-Za-z0-9]{3,}/", $comic_name))
            return $query->where($column, 'LIKE', '%' . $comic_name . '%');
        else // Sorry mom
            return $query->where(\DB::raw('1=2'));
    }

    public static function fullSearch($search) {
        return Comic::search($search)->get();
    }

    public static function publicSearch($search, $column='name') {
        return Comic::public()->search($search, $column)->get();
    }

    public static function buildPath($comic) {
        return 'comics/' . $comic->slug . '_' . $comic->salt;
    }

    public static function path($comic) {
        return 'public/' . Comic::buildPath($comic);
    }

    public static function absolutePath($comic) {
        return public_path() . '/storage/' . Comic::buildPath($comic);
    }

    public static function getThumbnailUrl($comic) {
        if ($comic->thumbnail === null || $comic->thumbnail === '') return null;
        return asset('storage/' . Comic::buildPath($comic) . '/' . urlencode($comic->thumbnail));
    }

    public static function getUrl($comic) {
        return "/comics/$comic->slug";
    }

    public static function getNextOrderIndex(): int {
        /*$max = Comic::max('order_index');
        return intval(($max ? $max : 0) + 1);*/
        return 1;
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
                    'required' => 1,
                ],
                'values' => ['max:191'],
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'slug',
                    'label' => 'URL slug',
                    'hint' => 'Automatically generated, use this if you want to have a custom URL slug',
                    'disabled' => 1,
                    'max' => '48',
                ],
                'values' => ['max:48'],
            ], [
                'type' => 'input_checkbox',
                'parameters' => [
                    'field' => 'hidden',
                    'label' => 'Hidden',
                    'hint' => 'Check to hide this comic',
                    'checked' => intval(config('settings.default_hidden_comic')),
                    'required' => 1,
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
                    'required' => 1,
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
            ], [
                'type' => 'input_text',
                'parameters' => [
                    'field' => 'order_index',
                    'label' => 'Order index',
                    'hint' => 'It is used to order the chapters (crescent order). You can use negative and decimal values. If empty it set the comic as current last',
                    'pattern' => '-?[0-9]+(?:\.[0-9]{1,3})?',
                    'default' => Comic::getNextOrderIndex(),
                    'required' => 1,
                ],
                'values' => ['numeric', 'regex:/^-?[0-9]+(?:\.[0-9]{1,3})?$/'],
            ],

        ];

    }

    public static function generateReaderArray($comic) {
        if (!$comic) return null;
        $genres = [];
        foreach (explode(',', $comic->genres) as $genre) {
            if ($genre != null) array_push($genres, ["name" => $genre, "slug" => Str::slug($genre)]);
        }
        return [
            'id' => Auth::check() && Auth::user()->hasPermission('manager') ? $comic->id : null,
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
            'hidden' => $comic['hidden'], // "->hidden" is the eloquent variable for hidden attributes
            'views' => intval(Chapter::public()->where('comic_id', $comic->id)->sum('views')),
            'rating' => round(Chapter::public()->where('comic_id', $comic->id)->avg('rating'), 2),
            'url' => Comic::getUrl($comic),
            'slug' => $comic->slug,
            'last_chapter' => Chapter::generateReaderArray($comic, Chapter::where([['comic_id', $comic->id], ['hidden', 0]])
                ->orderByDesc('created_at')->first()), // I want only the last truly public chapter
        ];
    }

    public static function generateReaderArrayWithChapters($comic) {
        if (!$comic) return null;
        $response = Comic::generateReaderArray($comic);
        $response['chapters'] = [];
        $response['volume_downloads'] = [];
        $chapters = $comic->publicChapters;
        foreach ($chapters as $chapter) {
            $chapter_array = Chapter::generateReaderArray($comic, $chapter);
            array_push($response['chapters'], $chapter_array);
            if($chapter_array['volume_download']){
                $download = VolumeDownload::where([['comic_id', $comic->id], ['language', $chapter->language], ['volume', $chapter->volume]])->first();
                if($download) $response['volume_downloads'][$download->name] = $chapter_array['volume_download'];
            }
        }
        return $response;
    }

    public static function getFormFieldsForValidation() {
        return getFormFieldsForValidation(Comic::getFormFields());
    }

    public static function getFieldsFromRequest($request, $form_fields) {
        $fields = getFieldsFromRequest($request, $form_fields);
        if (isset($fields['thumbnail']) && $fields['thumbnail']) {
            $fields['thumbnail'] = preg_replace("/%/", "", $request->file('thumbnail')->getClientOriginalName());
        } else {
            unset($fields['thumbnail']);
        }
        if (isset($fields['genres']) && $fields['genres']) {
            $fields['genres'] = trimCommas($fields['genres']);
        }
        return $fields;
    }

    public static function getFieldsIfValid($request) {
        $form_fields = Comic::getFormFieldsForValidation();
        $request->validate($form_fields);
        return Comic::getFieldsFromRequest($request, $form_fields);
    }

    public static function generateSlug($fields) {
        return generateSlug(new Comic, $fields);
    }
}
