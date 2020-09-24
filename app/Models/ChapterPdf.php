<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChapterPdf extends Model {
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

        public static function getPdf($comic, $chapter) {
        $pdf = ChapterPdf::where('chapter_id', $chapter->id)->first();
        $path = Chapter::path($comic, $chapter);

        if (!$pdf) {
            // Clear cache
            $max_cache = intval(config('settings.max_cache_pdf'));
            while ($max_cache > 0 && ChapterPdf::sum('size') > $max_cache) {
                $pdf_to_delete = ChapterPdf::orderBy('last_download', 'asc')->first();
                ChapterPdf::cleanPdf($pdf_to_delete);
            }

            $absolute_path = Chapter::absolutePath($comic, $chapter);
            $base_name = ChapterDownload::name($comic, $chapter);
            $pdf_name = Str::random() . '.pdf';
            $pdf_path = "$path/$pdf_name";
            $pdf_absolute_path = "$absolute_path/$pdf_name";
            $files = [];
            foreach ($chapter->pages as $page) {
                array_push($files, "$absolute_path/$page->filename");
            }
            createPdf($pdf_absolute_path, $files);
            if(Storage::missing($pdf_path)) return null;

            $pdf = ChapterPdf::create([
                'chapter_id' => $chapter->id,
                'name' => "$base_name.pdf",
                'filename' => $pdf_name,
                'size' => intval(Storage::size($pdf_path) / (1024 * 1024)),
            ]);
        }
            $pdf_path = "$path/$pdf->filename";
        if (!Storage::exists($pdf_path)) {
            ChapterPdf::cleanPdf($pdf, $comic, $chapter);
            return ChapterPdf::getPdf($comic, $chapter);
        }
        $pdf->timestamps = false;
        $pdf->last_download = Carbon::now();
        $pdf->save();
        return ['path' => $pdf_path, 'name' => $pdf->name];
    }

    public static function cleanPdf($pdf_to_delete, $comic = null, $chapter = null) {
        if ($pdf_to_delete) {
            if (!$chapter) $chapter = $pdf_to_delete->chapter;
            if (!$comic) $comic = $chapter->comic;
            cleanDirectoryByExtension(Chapter::path($comic, $chapter), 'pdf');
            $pdf_to_delete->delete();
        }
    }

}
