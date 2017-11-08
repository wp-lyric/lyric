<?php

namespace Lyric\Contracts\MetaBox;

use Lyric\Contracts\Fields\FieldFactory;
use Lyric\Hooks\BindToWordPress;

interface MetaBoxFactory extends BindToWordPress
{
    /**
     * MetaBoxFactory constructor.
     *
     * @param MetaBoxBuilder $metaBoxBuilder
     * @param FieldFactory   $fieldFactory
     * @param string         $postType
     */
    public function __construct(MetaBoxBuilder $metaBoxBuilder, FieldFactory $fieldFactory, $postType);

    /**
     * Add new MetaBoxBase class
     *
     * @param $metaBoxBase
     */
    public function addMetaBox($metaBoxBase);
}
