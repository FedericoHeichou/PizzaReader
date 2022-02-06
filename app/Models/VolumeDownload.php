<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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

    public function comic(): BelongsTo {
        return $this->belongsTo(Comic::class);
    }

    public static function name($comic, $language, $volume): string {
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

    // TODO se c'ho voglia:
    // Known possible bug: if you programmed a chapter publication the cache keep serving the old zip until you perform
    // a new update of a chapter changing important parameters (vol, ch, pages, etc) or parameters
    // like hidden, licensed, publish_start or publish_end
    public static function getDownload($comic, $language, $volume): ?array {
        $download = VolumeDownload::where([['comic_id', $comic->id], ['language', $language], ['volume', $volume]])->first();
        $path = Comic::path($comic);

        // Check if it is a dead download
        if ($download && $download->size === 0 && $download->created_at < Carbon::now()->subMinutes(15)) {
            VolumeDownload::cleanDownload($download, $comic);
            $download = null;
        }

        // If another user is creating the zip wait some seconds
        $tries = 0;
        while ($download && $download->size === 0) {
            sleep(1);
            $tries++;
            if ($tries > 25) return null;
            $download = VolumeDownload::where([['comic_id', $comic->id], ['language', $language], ['volume', $volume]])->first();
        }

        // If doesn't exists or the creating zip of other user failed
        if (!$download) {
            $absolute_path = Comic::absolutePath($comic);
            $base_name = VolumeDownload::name($comic, $language, $volume);
            $zip_name = Str::random() . '.zip';
            $zip_path = "$path/$zip_name";
            $zip_absolute_path = "$absolute_path/$zip_name";
            $length_of_path = strlen($path . '/');
            $files = [];

            // Lock the zip creation
            $download = VolumeDownload::create([
                'comic_id' => $comic->id,
                'language' => $language,
                'volume' => $volume,
                'name' => "$base_name.zip",
                'filename' => $zip_name,
                'size' => 0,
            ]);

            // Clear cache
            $max_cache = intval(config('settings.max_cache_download'));
            while ($max_cache > 0 && VolumeDownload::sum('size') > $max_cache) {
                $download_to_delete = VolumeDownload::orderBy('last_download', 'asc')->first();
                VolumeDownload::cleanDownload($download_to_delete, $comic);
            }

            $chapters = $comic->chapters()->published()->where('licensed', 0)->where('volume', $volume)->get();
            $chapter_ids = $chapters->pluck('id')->toArray();
            foreach ($chapters as $chapter) {
                $chapter_download = ChapterDownload::getDownload($comic, $chapter, $chapter_ids);
                if ($chapter_download === null) {
                    Log::error("ChapterDownload::getDownload:",
                        ['comic' => $comic, 'chapter' => $chapter, 'chapter_ids' => $chapter_ids]
                    );
                    $download->delete();
                    return null;
                }
                $files[] = [
                    'source' => "$absolute_path/" . substr($chapter_download['path'], $length_of_path),
                    'dest' => "$base_name/" . $chapter_download['name'],
                ];
            }
            createZip($zip_absolute_path, $files);
            if(Storage::missing($zip_path)) {
                $download->delete();
                return null;
            }

            // Unlock the zip creation
            $download->size = 1 + intval(Storage::size($zip_path) / (1024 * 1024));
            $download->save();
        }

        // If the zip doesn't exist
        $zip_path = "$path/$download->filename";
        if (Storage::missing($zip_path)) {
            VolumeDownload::cleanDownload($download, $comic);
            return VolumeDownload::getDownload($comic, $language, $volume);
        }

        // Refresh download
        $download->timestamps = false;
        $download->last_download = Carbon::now();
        $download->save();
        return ['path' => $zip_path, 'name' => $download->name];
    }

    public static function cleanDownload($download_to_delete, $comic = null) {
        if (!$download_to_delete) return;
        if (!$comic) $comic = $download_to_delete->comic;
        $download_path = Comic::path($comic) . '/' . $download_to_delete->filename;
        if(Storage::exists($download_path)) Storage::delete($download_path);
        $download_to_delete->delete();
    }

}
