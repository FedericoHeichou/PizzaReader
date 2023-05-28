<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class Comic extends Model {
    protected $fillable = [
        'name', 'slug', 'salt', 'hidden', 'author', 'artist', 'target', 'genres', 'status', 'description', 'thumbnail',
        'custom_chapter', 'comic_format_id', 'adult', 'order_index', 'alt_titles',
    ];

    protected $casts = [
        'id' => 'integer',
        'hidden' => 'integer',
        'comic_format_id' => 'integer',
        'adult' => 'integer',
        'order_index' => 'float',
    ];

    public function scopePublished($query) {
        return $query->where('hidden', 0);
    }

    public function scopePublic($query, $ord="order_index") {
        $query = $query->orderBy($ord);
        if ($ord !== 'name') $query = $query->orderBy('name');
        if (!Auth::check() || !Auth::user()->hasPermission('checker'))
            return $query->published();
        else if (Auth::user()->hasPermission('manager') || Auth::user()->all_comics)
            return $query;
        else {
            $comics = Auth::user()->comics()->select('comic_id');
            return $query->where(function ($q) use ($comics) {
                $q->published()->orWhereIn('id', $comics);
            });
        }
    }

    public function format() {
        return $this->belongsTo(ComicFormat::class);
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function chapters($order=true) {
        return $order ? $this->hasMany(Chapter::class)
            ->orderByDesc('volume')
            ->orderByDesc('chapter')
            ->orderByDesc('subchapter')
            ->orderByDesc('language') : $this->hasMany(Chapter::class);
    }

    public function volume_downloads() {
        return $this->hasMany(VolumeDownload::class);
    }

    public function publishedChapters() {
        return $this->chapters()->published();
    }

    public function publicChapters() {
        return $this->chapters()->public();
    }

    public function lastPublishedChapter() {
        return $this->chapters(false)->published()->orderBy('published_on', 'desc')->first();
    }

    public function views_list(): HasManyThrough {
        return $this->hasManyThrough(View::class, Chapter::class);
    }

    public static function slug($slug) {
        return Comic::where('slug', $slug)->first();
    }

    public static function publicSlug($slug) {
        return Comic::public()->where('slug', $slug)->first();
    }

    public function scopeSearch($query, $search, $column='name') {
        $comic_name = preg_replace("/[^A-Za-z0-9]/", '_', $search);
        if(preg_match("/[A-Za-z0-9]{3,}/", $comic_name)) {
            if ($column === 'name') {
                return $query->where(function ($q) use ($comic_name) {
                    $q->where('name', 'LIKE', '%' . $comic_name . '%')->orWhere('alt_titles', 'LIKE', '%' . $comic_name . '%');
                });
            } else {
                return $query->where($column, 'LIKE', '%' . $comic_name . '%');
            }
        } else { // Sorry mom
            return $query->where(DB::raw('1=2'));
        }
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

    public static function storagePath($comic) {
        return storage_path('app/' . Comic::path($comic));
    }

    public static function getThumbnailUrl($comic) {
        if ($comic->thumbnail === null || $comic->thumbnail === '') return null;
        $thumbnail_path = 'storage/' . Comic::buildPath($comic) . '/';
        return File::exists($thumbnail_path . $comic->thumbnail) ? asset($thumbnail_path . rawurlencode($comic->thumbnail)) : null;
    }

    public static function getUrl($comic) {
        return "/comics/$comic->slug";
    }

    public static function getNextOrderIndex(): int {
        /*$max = Comic::max('order_index');
        return intval(($max ? $max : 0) + 1);*/
        return 0;
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
                    'disabled' => true,
                    'edit' => true,
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
                'type' => 'textarea',
                'parameters' => [
                    'field' => 'alt_titles',
                    'label' => 'Alternative titles',
                    'hint' => 'Insert alternative titles (one per line)',
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
                    'label' => 'Recommended index',
                    'hint' => 'Set it different from 0 to show the comic in the "Recommended" tab. It is used to order the chapters (crescent order) in "Recommended" tab. You can use negative and decimal values',
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
        $exploded_genres = explode(',', $comic->genres);
        sort($exploded_genres);
        foreach ($exploded_genres as $genre) {
            if ($genre != null) $genres[] = ['name' => $genre, 'slug' => Str::slug($genre)];
        }
        $thumbnail = Comic::getThumbnailUrl($comic) ?: asset(config('settings.cover_path'));
        return [
            'id' => Auth::check() && Auth::user()->hasPermission('manager') ? $comic->id : null,
            'title' => $comic->name,
            'thumbnail' => $thumbnail,
            'thumbnail_small' => getSmallThumbnail($thumbnail),
            'description' => $comic->description,
            'alt_titles' => $comic->alt_titles ? array_filter(explode("\n", $comic->alt_titles), 'strlen') : [],
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
            'rating' => round(Chapter::public()->where('comic_id', $comic->id)->whereNotNull('rating')->avg('rating'), 2) ?: null,
            'url' => Comic::getUrl($comic),
            'slug' => $comic->slug,
            'recommended' => $comic->order_index,
            'last_chapter' => Chapter::generateReaderArray($comic, $comic->lastPublishedChapter()), // I want only the last truly public chapter
        ];
    }

    public static function generateReaderArrayWithChapters($comic, $show_licensed=true) {
        if (!$comic) return null;
        $response = Comic::generateReaderArray($comic);
        $response['chapters'] = [];
        $response['volume_downloads'] = [];
        $chapters = $comic->publicChapters;
        foreach ($chapters as $chapter) {
            if (!$show_licensed && Chapter::isLicensed($chapter)) continue;
            $chapter_array = Chapter::generateReaderArray($comic, $chapter);
            $response['chapters'][] = $chapter_array;
            if($chapter_array['volume_download']){
                $response['volume_downloads'][VolumeDownload::name($comic, $chapter->language, $chapter->volume)] = $chapter_array['volume_download'];
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
            $fields['genres'] = trimChar($fields['genres'], ",");
        }
        if (isset($fields['alt_titles']) && $fields['alt_titles']) {
            $fields['alt_titles'] = trimChar($fields['alt_titles'], "\n");
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

    public static function getStats($comic): array {        
        $date_format = 'DATE(`' . DB::getTablePrefix() . 'views`.`created_at`)';
        $views = $comic->views_list()
            ->select(DB::raw($date_format .' as view_date'), DB::raw('COUNT(*) AS views'))
            ->groupBy(
                DB::raw($date_format),
                'comic_id', // TODO remove this when the laravel_through_key will be removed with groupBy
                            // https://github.com/laravel/framework/issues/47260
            )
            ->orderBy('view_date')
            ->get();
        $views_per_day = [];
        foreach ($views as $view) {
            $views_per_day[$view->view_date] = $view->views;
        }
        return [
            'views_per_day' => $views_per_day,
        ];
    }
}
