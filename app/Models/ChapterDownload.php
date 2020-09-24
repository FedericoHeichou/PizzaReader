<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChapterDownload extends Model {
    protected $fillable = [
        'chapter_id', 'name', 'filename', 'size', 'last_download'
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
        $path = Chapter::path($comic, $chapter);

        if (!$download) {
            // Clear cache
            $max_cache = intval(config('settings.max_cache_download'));
            while ($max_cache > 0 && ChapterDownload::sum('size') > $max_cache) {
                $download_to_delete = ChapterDownload::orderBy('last_download', 'asc')->first();
                ChapterDownload::cleanDownload($download_to_delete);
            }

            $absolute_path = Chapter::absolutePath($comic, $chapter);
            $base_name = ChapterDownload::name($comic, $chapter);
            $zip_name = Str::random() . '.zip';
            $zip_path = "$path/$zip_name";
            $zip_absolute_path = "$absolute_path/$zip_name";
            $files = [];
            foreach ($chapter->pages as $page) {
                array_push($files, [
                    'source' => "$absolute_path/$page->filename",
                    'dest' => "$base_name/$page->filename"
                ]);
            }
            createZip($zip_absolute_path, $files);
            if(Storage::missing($zip_path)) return null;

            $download = ChapterDownload::create([
                'chapter_id' => $chapter->id,
                'name' => "$base_name.zip",
                'filename' => $zip_name,
                'size' => intval(Storage::size($zip_path) / (1024 * 1024)),
            ]);
        }
        $zip_path = "$path/$download->filename";
        if (!Storage::exists($zip_path)) {
            ChapterDownload::cleanDownload($download, $comic, $chapter);
            return ChapterDownload::getDownload($comic, $chapter);
        }
        $download->timestamps = false;
        $download->last_download = Carbon::now();
        $download->save();
        return ['path' => $zip_path, 'name' => $download->name];
    }

    public static function cleanDownload($download_to_delete, $comic = null, $chapter = null, $old_chapter = null) {
        if ($download_to_delete) {
            if (!$chapter) $chapter = $download_to_delete->chapter;
            if (!$comic) $comic = $chapter->comic;
            cleanDirectoryByExtension(Chapter::path($comic, $chapter), 'zip');
            $download_to_delete->delete();
        }
        if($chapter) {
            $pdf_to_delete = $chapter->pdf;
            if($pdf_to_delete) ChapterPdf::cleanPdf($pdf_to_delete, $comic, $chapter);
        }
        // If $old_chapter is set it means we need to delete its (old) volume zip too
        // It doesn't really matter that $old_chapter is equals to $chapter because for example if we update pages
        // we still need to delete its volume
        if ($old_chapter && !$old_chapter['hidden']) {
            VolumeDownload::cleanDownload(Chapter::volume_download($old_chapter));
        }
    }

}
