<?php

namespace Tests\Unit;

use App\Http\Controllers\Reader\ReaderController;
use PHPUnit\Framework\TestCase;

class ExplodeChTest extends TestCase
{
    // ── valid cases ───────────────────────────────────────────────────────────

    public function test_null_ch_returns_lang_only_array(): void
    {
        $result = ReaderController::explodeCh('en', null);
        $this->assertSame([
            'lang' => 'en',
            'vol' => null,
            'ch' => null,
            'sub' => null,
        ], $result);
    }

    public function test_empty_string_ch_returns_lang_only_array(): void
    {
        $result = ReaderController::explodeCh('en', '');
        $this->assertSame([
            'lang' => 'en',
            'vol' => null,
            'ch' => null,
            'sub' => null,
        ], $result);
    }

    public function test_ch_only(): void
    {
        $result = ReaderController::explodeCh('en', 'ch/1');
        $this->assertSame([
            'lang' => 'en',
            'vol' => null,
            'ch' => '1',
            'sub' => null,
        ], $result);
    }

    public function test_vol_only(): void
    {
        $result = ReaderController::explodeCh('en', 'vol/2');
        $this->assertSame([
            'lang' => 'en',
            'vol' => '2',
            'ch' => null,
            'sub' => null,
        ], $result);
    }

    public function test_vol_and_ch(): void
    {
        $result = ReaderController::explodeCh('en', 'vol/1/ch/5');
        $this->assertSame([
            'lang' => 'en',
            'vol' => '1',
            'ch' => '5',
            'sub' => null,
        ], $result);
    }

    public function test_vol_ch_and_sub(): void
    {
        $result = ReaderController::explodeCh('jp', 'vol/3/ch/12/sub/1');
        $this->assertSame([
            'lang' => 'jp',
            'vol' => '3',
            'ch' => '12',
            'sub' => '1',
        ], $result);
    }

    public function test_ch_with_leading_slash(): void
    {
        $result = ReaderController::explodeCh('en', '/ch/7');
        $this->assertSame([
            'lang' => 'en',
            'vol' => null,
            'ch' => '7',
            'sub' => null,
        ], $result);
    }

    public function test_ch_with_trailing_slash(): void
    {
        $result = ReaderController::explodeCh('en', 'ch/3/');
        $this->assertSame([
            'lang' => 'en',
            'vol' => null,
            'ch' => '3',
            'sub' => null,
        ], $result);
    }

    // ── invalid cases ─────────────────────────────────────────────────────────

    public function test_odd_number_of_segments_returns_null(): void
    {
        $this->assertNull(ReaderController::explodeCh('en', 'ch'));
    }

    public function test_three_segments_returns_null(): void
    {
        $this->assertNull(ReaderController::explodeCh('en', 'vol/1/ch'));
    }

    // ── different languages ────────────────────────────────────────────────────

    public function test_different_language_codes(): void
    {
        foreach (['en', 'es', 'fr', 'it', 'pt', 'jp'] as $lang) {
            $result = ReaderController::explodeCh($lang, 'ch/1');
            $this->assertSame($lang, $result['lang']);
        }
    }
}
