<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VolumeDownload extends Model {
    protected $fillable = [
        'comic_id', 'language', 'volume', 'name', 'filename', 'size', 'last_download'
    ];

    protected $casts = [
        'id' => 'integer',
        'comic_id' => 'integer',
        'volume' => 'integer',
        'size' => 'integer',
        'last_download' => 'datetime',
    ];

    public function comic() {
        return $this->belongsTo(Comic::class);
    }

    public static function name($comic, $language, $volume) {
        $name = preg_replace('/__+/', '_', preg_replace('/[^a-zA-Z0-9]/', '_', $comic->name)) . '_Volume_';
        if ($volume < 10) {
            $name .= '00' . $volume;
        } elseif ($volume < 100) {
            $name .= '0' . $volume;
        } else {
            $name .= $volume;
        }
        $name .= '[' . strtoupper($language) . ']';
        return $name;
    }

    public static function getDownload($comic, $language, $volume) {
        $download = VolumeDownload::where([['comic_id', $comic->id], ['language', $language], ['volume', $volume]])->first();
        $path = Comic::path($comic);

        if (!$download) {
            // Clear cache
            $max_cache = intval(config('settings.max_cache_download'));
            while ($max_cache > 0 && VolumeDownload::sum('size') > $max_cache) {
                $download_to_delete = VolumeDownload::orderBy('last_download', 'asc')->first();
                VolumeDownload::cleanDownload($download_to_delete, $comic);
            }

            $absolute_path = Comic::absolutePath($comic);
            $base_name = VolumeDownload::name($comic, $language, $volume);
            $zip_name = Str::random() . '.zip';
            $zip_path = "$path/$zip_name";
            $zip_absolute_path = "$absolute_path/$zip_name";
            $length_of_path = strlen($path . '/');
            $files = [];

            foreach ($comic->chapters()->where('hidden', 0)->get() as $chapter) {
                $chapter_download = ChapterDownload::getDownload($comic, $chapter);
                array_push($files, [
                    'source' => "$absolute_path/" . substr($chapter_download['path'], $length_of_path),
                    'dest' => "$base_name/" . $chapter_download['name'],
                ]);
            }
            createZip($zip_absolute_path, $files);
            if(Storage::missing($zip_path)) return null;

            $download = VolumeDownload::create([
                'comic_id' => $comic->id,
                'language' => $language,
                'volume' => $volume,
                'name' => "$base_name.zip",
                'filename' => $zip_name,
                'size' => intval(Storage::size($zip_path) / (1024 * 1024)),
            ]);
        }
        $zip_path = "$path/$download->filename";
        if (!Storage::exists($zip_path)) {
            VolumeDownload::cleanDownload($download, $comic);
            return VolumeDownload::getDownload($comic, $language, $volume);
        }
        $download->timestamps = false;
        $download->last_download = Carbon::now();
        $download->save();
        return ['path' => $zip_path, 'name' => $download->name];
    }

    public static function cleanDownload($download_to_delete, $comic = null) {
        if (!$download_to_delete) return;
        if (!$comic) $comic = $download_to_delete->comic;
        Storage::delete(Comic::path($comic) . '/' . $download_to_delete->filename);
        $download_to_delete->delete();
    }

}
