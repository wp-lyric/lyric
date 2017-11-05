<?php

namespace LyricTests\OptionsPages\Fixtures;

use Lyric\OptionsPages\PageBase;
use Lyric\Contracts\Fields\FieldFactory;
use Lyric\Contracts\OptionsPages\PageBuilder;

class OptionsPageFaker extends PageBase
{
    /**
     * Build Page
     *
     * @param PageBuilder $pageBuilder
     *
     * @return PageBuilder
     */
    protected function page(PageBuilder $pageBuilder) {
        return $pageBuilder;
    }

    /**
     * Return array of the Fields instances
     *
     * @param FieldFactory $fieldFactory
     *
     * @return array
     */
    protected function fields(FieldFactory $fieldFactory) {
        return [];
    }
}