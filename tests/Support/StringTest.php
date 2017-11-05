<?php

namespace LyricTests\Support;

use Lyric\Support\Strings;
use PHPUnit\Framework\TestCase;

class StringTest extends TestCase
{
    /**
     * Change pascal case to human format
     */
    public function testChangePascalCaseToHumanFormat()
    {
        $this->assertEquals('Final Result', Strings::pascalCaseToHuman('FinalResult'));
        $this->assertEquals('Final Result', Strings::pascalCaseToHuman('Final Result'));
    }

    /**
     * Change string to slug format
     */
    public function testChangeStringToSlugFormat()
    {
        $this->assertEquals('final-result', Strings::slug('FinalResult'));
        $this->assertEquals('final-result', Strings::slug('Final Result'));
    }
}
