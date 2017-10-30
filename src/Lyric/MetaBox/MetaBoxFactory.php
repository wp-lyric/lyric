<?php

namespace Lyric\MetaBox;

use Lyric\Contracts\Fields\FieldFactory;
use Lyric\Contracts\MetaBox\MetaBoxBuilder;

class MetaBoxFactory
{
    protected $metaBoxBuilder;

    protected $fieldFactory;

    protected $metaBoxes = [];
    /**
     * MetaBoxFactory constructor.
     */
    public function __construct(MetaBoxBuilder $metaBoxBuilder, FieldFactory $fieldFactory, $postType)
    {
        $metaBoxBuilder = $metaBoxBuilder->setPostType($postType);
        $this->metaBoxBuilder = $metaBoxBuilder;
        $this->fieldFactory = $fieldFactory;
    }

    /**
     * Add new MetaBoxBase class
     *
     * @param $metaBoxBase
     */
    public function addMetaBox($metaBoxBase)
    {

        $metaBox = new $metaBoxBase(clone $this->metaBoxBuilder, $this->fieldFactory);
        $this->metaBoxes[] = $metaBox;

        return $this;
    }

    /**
     * Bind all TaxonomyBase classes
     */
    public function bind()
    {
        foreach ($this->metaBoxes as $metaBox) {
            if($metaBox instanceof MetaBoxBase) {
                $metaBox->bind();
            }
        }
    }
}