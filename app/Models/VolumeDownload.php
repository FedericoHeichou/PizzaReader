<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class VolumeDownload extends Model {
    protected $fillable = [
        'comic_id', 'language', 'volume', 'size', 'last_download'
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
        $zip_name = VolumeDownload::name($comic, $language, $volume);
        $path = Comic::path($comic);
        $zip_path = $path . '/' . $zip_name . '.zip';
        $absolute_path = Comic::absolutePath($comic);

        if (!$download) {
            // Clear cache
            $max_cache = intval(config('settings.max_cache_download'));
            while ($max_cache > 0 && VolumeDownload::sum('size') > $max_cache) {
                $download_to_delete = VolumeDownload::orderBy('last_download', 'asc')->first();
                VolumeDownload::cleanDownload($download_to_delete, $comic);
            }

            $length_of_path = strlen($path . '/');
            $chapters = [];
            foreach ($comic->chapters()->where('hidden', 0)->get() as $chapter) {
                array_push($chapters, substr(ChapterDownload::getDownload($comic, $chapter), $length_of_path));
            }
            createZip($chapters, $absolute_path, $zip_name);
            $download = VolumeDownload::create([
                'comic_id' => $comic->id,
                'language' => $language,
                'volume' => $volume,
                'size' => intval(Storage::size($zip_path) / (1024 * 1024)),
            ]);
        }
        if (!Storage::exists($zip_path)) {
            cleanDirectoryByExtension($path, 'zip');
            $download->delete();
            return VolumeDownload::getDownload($comic, $language, $volume);
        }
        $download->last_download = Carbon::now();
        $download->save();
        return $zip_path;
    }

    public static function cleanDownload($download_to_delete, $comic = null) {
        if (!$download_to_delete) return;
        if (!$comic) $comic = $download_to_delete->comic;
        cleanDirectoryByExtension(Comic::path($comic), 'zip');
        $download_to_delete->delete();
    }

}
