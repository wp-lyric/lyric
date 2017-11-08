<?php

namespace Lyric\MetaBox;

use Lyric\Contracts\Fields\FieldFactory;
use Lyric\Contracts\MetaBox\MetaBoxBuilder;
use Lyric\Contracts\MetaBox\MetaBoxFactory as MetaBoxFactoryContract;
use Lyric\Hooks\BindToWordPress;

class MetaBoxFactory implements MetaBoxFactoryContract
{
    /**
     * MetaBoxBuilder instance
     * @var MetaBoxBuilder
     */
    protected $metaBoxBuilder;

    /**
     * FieldFactory instance
     * @var FieldFactory
     */
    protected $fieldFactory;

    /**
     * List of the MetaBoxBase
     * @var array
     */
    protected $metaBoxes = [];

    /**
     * MetaBoxFactory constructor
     *
     * @param MetaBoxBuilder $metaBoxBuilder
     * @param FieldFactory   $fieldFactory
     * @param string         $postType
     */
    public function __construct(MetaBoxBuilder $metaBoxBuilder, FieldFactory $fieldFactory, $postType)
    {
        $metaBoxBuilder->setPostType($postType);
        $this->metaBoxBuilder = $metaBoxBuilder;
        $this->fieldFactory = $fieldFactory;
    }

    /**
     * Add new MetaBoxBase class
     *
     * @param $metaBoxBase
     *
     * @return $this
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
            if ($metaBox instanceof BindToWordPress) {
                $metaBox->bind();
            }
        }
    }
}
