<?php

namespace LyricTests\PostTypes\Fixtures;

use Lyric\Contracts\Fields\FieldFactory;
use Lyric\Contracts\MetaBox\MetaBoxBuilder;
use Lyric\MetaBox\MetaBoxBase;

class MetaBoxFaker extends MetaBoxBase
{
    /**
     * @param MetaBoxBuilder $metaBoxBuilder
     *
     * @return mixed
     */
    protected function settings(MetaBoxBuilder $metaBoxBuilder)
    {
        return $metaBoxBuilder;
    }

    /**
     * @param FieldFactory $fieldFactory
     *
     * @return mixed
     */
    protected function fields(FieldFactory $fieldFactory)
    {
        return [$fieldFactory];
    }
}
