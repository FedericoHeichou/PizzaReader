<?php

namespace Tests\Unit;

use App\Models\Chapter;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Chapter::name() and Chapter::getVolChSub()
 */
class ChapterNameTest extends TestCase
{
    private function makeChapter(array $attributes = []): Chapter
    {
        $chapter = new Chapter();
        $chapter->volume = $attributes['volume'] ?? null;
        $chapter->chapter = $attributes['chapter'] ?? null;
        $chapter->subchapter = $attributes['subchapter'] ?? null;
        $chapter->title = $attributes['title'] ?? null;
        $chapter->prefix = $attributes['prefix'] ?? null;
        return $chapter;
    }

    private function makeComic(string $custom_chapter = null): object
    {
        return (object)['custom_chapter' => $custom_chapter];
    }

    // ── getVolChSub ───────────────────────────────────────────────────────────

    public function test_getVolChSub_all_set(): void
    {
        $chapter = $this->makeChapter(['volume' => 1, 'chapter' => 5, 'subchapter' => 3]);
        $this->assertSame('Vol.1 Ch.5.3', Chapter::getVolChSub($chapter));
    }

    public function test_getVolChSub_chapter_only(): void
    {
        $chapter = $this->makeChapter(['chapter' => 7]);
        $this->assertSame('Ch.7', Chapter::getVolChSub($chapter));
    }

    public function test_getVolChSub_volume_and_chapter(): void
    {
        $chapter = $this->makeChapter(['volume' => 2, 'chapter' => 10]);
        $this->assertSame('Vol.2 Ch.10', Chapter::getVolChSub($chapter));
    }

    public function test_getVolChSub_empty_when_all_null(): void
    {
        $chapter = $this->makeChapter();
        $this->assertSame('', Chapter::getVolChSub($chapter));
    }

    public function test_getVolChSub_volume_zero(): void
    {
        $chapter = $this->makeChapter(['volume' => 0, 'chapter' => 1]);
        $this->assertSame('Vol.0 Ch.1', Chapter::getVolChSub($chapter));
    }

    // ── name (no custom_chapter) ──────────────────────────────────────────────

    public function test_name_oneshot_when_all_null(): void
    {
        $chapter = $this->makeChapter();
        $comic = $this->makeComic();
        $this->assertSame('Oneshot', Chapter::name($comic, $chapter));
    }

    public function test_name_chapter_only(): void
    {
        $chapter = $this->makeChapter(['chapter' => 3]);
        $comic = $this->makeComic();
        $this->assertSame('Ch.3', Chapter::name($comic, $chapter));
    }

    public function test_name_chapter_and_title(): void
    {
        $chapter = $this->makeChapter(['chapter' => 3, 'title' => 'The Beginning']);
        $comic = $this->makeComic();
        $this->assertSame('Ch.3 - The Beginning', Chapter::name($comic, $chapter));
    }

    public function test_name_title_only(): void
    {
        $chapter = $this->makeChapter(['title' => 'Prologue']);
        $comic = $this->makeComic();
        $this->assertSame('Prologue', Chapter::name($comic, $chapter));
    }

    public function test_name_prefix_prepended(): void
    {
        $chapter = $this->makeChapter(['chapter' => 1, 'prefix' => 'Extra']);
        $comic = $this->makeComic();
        $this->assertSame('Extra Ch.1', Chapter::name($comic, $chapter));
    }

    // ── name (with custom_chapter) ────────────────────────────────────────────

    public function test_name_custom_chapter_basic_num(): void
    {
        $chapter = $this->makeChapter(['chapter' => 5]);
        $comic = $this->makeComic('{num}');
        $this->assertSame('5', Chapter::name($comic, $chapter));
    }

    public function test_name_custom_chapter_vol_and_num(): void
    {
        $chapter = $this->makeChapter(['volume' => 1, 'chapter' => 2]);
        $comic = $this->makeComic('Vol.{vol} Ch.{num}');
        $this->assertSame('Vol.1 Ch.2', Chapter::name($comic, $chapter));
    }

    public function test_name_custom_chapter_conditional_vol_present(): void
    {
        $chapter = $this->makeChapter(['volume' => 1, 'chapter' => 2]);
        $comic = $this->makeComic('{vol:Vol.}{vol} {num}');
        $this->assertSame('Vol.1 2', Chapter::name($comic, $chapter));
    }

    public function test_name_custom_chapter_conditional_vol_absent(): void
    {
        $chapter = $this->makeChapter(['chapter' => 2]);
        $comic = $this->makeComic('{vol:Vol.}{vol} {num}');
        $this->assertSame(' 2', Chapter::name($comic, $chapter));
    }

    public function test_name_custom_ordinal_2nd(): void
    {
        $chapter = $this->makeChapter(['chapter' => 2]);
        $comic = $this->makeComic('{num}{ord}');
        $this->assertSame('2nd', Chapter::name($comic, $chapter));
    }

    public function test_name_custom_ordinal_3rd(): void
    {
        $chapter = $this->makeChapter(['chapter' => 3]);
        $comic = $this->makeComic('{num}{ord}');
        $this->assertSame('3rd', Chapter::name($comic, $chapter));
    }

    public function test_name_custom_ordinal_11th(): void
    {
        // The implementation uses substr($name, -1) so 11 → last digit '1' → 'st'
        $chapter = $this->makeChapter(['chapter' => 11]);
        $comic = $this->makeComic('{num}{ord}');
        $this->assertSame('11st', Chapter::name($comic, $chapter));
    }

    // ── slugLangVolChSub ───────────────────────────────────────────────────────

    public function test_slugLangVolChSub_all_set(): void
    {
        $chapter = $this->makeChapter(['volume' => 1, 'chapter' => 5, 'subchapter' => 3]);
        $chapter->language = 'en';
        $this->assertSame('en-1-5-3', Chapter::slugLangVolChSub($chapter));
    }

    public function test_slugLangVolChSub_nulls_become_N(): void
    {
        $chapter = $this->makeChapter();
        $chapter->language = 'en';
        $this->assertSame('en-N-N-N', Chapter::slugLangVolChSub($chapter));
    }
}
