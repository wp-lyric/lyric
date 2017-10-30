<?php

namespace Lyric\Contracts\MetaBox;

use Lyric\Contracts\Fields\FieldFactory;

interface MetaBoxFactory
{
    /**
     * MetaBoxFactory constructor.
     */
    public function __construct(MetaBoxBuilder $metaBoxBuilder, FieldFactory $fieldFactory, $postType);

    /**
     * Add new MetaBoxBase class
     *
     * @param $metaBoxBase
     */
    public function addMetaBox($metaBoxBase);

    /**
     * Bind all TaxonomyBase classes
     */
    public function bind();
}