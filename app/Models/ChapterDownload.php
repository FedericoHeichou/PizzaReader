<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ChapterDownload extends Model {
    protected $fillable = [
        'chapter_id', 'size', 'last_download'
    ];

    protected $casts = [
        'id' => 'integer',
        'chapter_id' => 'integer',
        'size' => 'integer',
        'last_download' => 'datetime',
    ];

    public function chapter() {
        return $this->belongsTo(Chapter::class);
    }

    public static function name($comic, $chapter) {
        $name = preg_replace('/__+/', '_', preg_replace('/[^a-zA-Z0-9]/', '_', $comic->name));
        if ($chapter->volume !== null) {
            $name .= '_v';
            if ($chapter->volume < 10) {
                $name .= '00' . $chapter->volume;
            } elseif ($chapter->volume < 100) {
                $name .= '0' . $chapter->volume;
            } else {
                $name .= $chapter->volume;
            }
        }
        if ($chapter->chapter !== null) {
            $name .= '_ch';
            if ($chapter->chapter < 10) {
                $name .= '000' . $chapter->chapter;
            } elseif ($chapter->chapter < 100) {
                $name .= '00' . $chapter->chapter;
            } elseif ($chapter->chapter < 1000) {
                $name .= '0' . $chapter->chapter;
            } else {
                $name .= $chapter->chapter;
            }
            if ($chapter->subchapter !== null) {
                $name .= '.';
            }
        }
        if ($chapter->subchapter !== null) {
            $name .= $chapter->subchapter;
        }
        $name .= '[' . strtoupper($chapter->language) . ']';
        if ($chapter->team_id !== null) {
            $name .= '[' . preg_replace('/__+/', '_', preg_replace('/[^a-zA-Z0-9]/', '_', Team::find($chapter->team_id)->name)) . ']';
        }
        return $name;
    }

    public static function getDownload($comic, $chapter) {
        $download = ChapterDownload::where('chapter_id', $chapter->id)->first();
        $zip_name = ChapterDownload::name($comic, $chapter);
        $path = Chapter::path($comic, $chapter);
        $zip_path = $path . '/' . $zip_name . '.zip';
        $absolute_path = Chapter::absolutePath($comic, $chapter);

        if (!$download) {
            // Clear cache
            $max_cache = intval(config('settings.max_cache_download'));
            while ($max_cache > 0 && ChapterDownload::sum('size') > $max_cache) {
                $download_to_delete = ChapterDownload::orderBy('last_download', 'asc')->first();
                ChapterDownload::cleanDownload($download_to_delete);
            }

            $pages = $chapter->pages->pluck('filename');
            createZip($pages, $absolute_path, $zip_name);

            $download = ChapterDownload::create([
                'chapter_id' => $chapter->id,
                'size' => intval(Storage::size($zip_path) / (1024 * 1024)),
            ]);
        }
        if (!Storage::exists($zip_path)) {
            cleanDirectoryByExtension($path, 'zip');
            $download->delete();
            return ChapterDownload::getDownload($comic, $chapter);
        }
        $download->last_download = Carbon::now();
        $download->save();
        return $zip_path;
    }

    public static function cleanDownload($download_to_delete, $comic = null, $chapter = null, $old_chapter = null) {
        if($download_to_delete){
            if(!$chapter) $chapter = $download_to_delete->chapter;
            if(!$comic) $comic = $chapter->comic;
            cleanDirectoryByExtension(Chapter::path($comic, $chapter), 'zip');
            $download_to_delete->delete();
        }
        // If $old_chapter is set it means we need to delete its (old) volume zip too
        // It doesn't really matter that $old_chapter is equals to $chapter because for example if we update pages
        // we still need to delete its volume
        if($old_chapter && !$old_chapter['hidden']) {
            VolumeDownload::cleanDownload(Chapter::volume_download($old_chapter));
        }
    }

}
