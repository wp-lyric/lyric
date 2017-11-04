<?php

namespace LyricTests\MetaBox\Fixtures;

use Lyric\MetaBox\MetaBoxBase;
use Lyric\Contracts\Fields\FieldFactory;
use Lyric\Contracts\MetaBox\MetaBoxBuilder;

class MetaBoxBaseFake extends MetaBoxBase
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
        return $fieldFactory;
    }
}