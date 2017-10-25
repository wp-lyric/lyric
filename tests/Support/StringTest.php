<?php

namespace LyricTests\Support;

use Lyric\Support\Strings;
use PHPUnit\Framework\TestCase;

class StringTest extends TestCase
{
    public function test_change_pascal_case_to_human_format()
    {
        $this->assertEquals('Final Result', Strings::pascalCaseToHuman('FinalResult'));
        $this->assertEquals('Final Result', Strings::pascalCaseToHuman('Final Result'));
    }

    public function test_change_string_to_slug_format()
    {
        $this->assertEquals('final-result', Strings::slug('FinalResult'));
        $this->assertEquals('final-result', Strings::slug('Final Result'));
    }
}
