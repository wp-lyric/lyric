<?php

namespace LyricTests\Utils;

use Lyric\Utils\I18n;
use PHPUnit\Framework\TestCase;

class I18nTest extends TestCase
{
    public function test_should_set_text_domain()
    {
        $i18n = $this->getMockForTrait(I18n::class);

        $i18n->setTextDomain('new-text-domain');

        $this->assertEquals('new-text-domain', $i18n->textDomain());
    }
}
