<?php

namespace Lyric\MetaBox;

use Lyric\Contracts\MetaBox\MetaBoxBase as MetaBoxBaseContract;
use Lyric\Contracts\MetaBox\MetaBoxBuilder;
use Lyric\Contracts\Fields\FieldBuilder;
use Lyric\Contracts\PostTypes\PostTypeBase;

abstract class MetaBoxBase implements MetaBoxBaseContract
{
    /**
     * Post type name
     *
     * @var string|PostTypeBase
     */
    protected $postType;

    /**
     * MetaBox priority
     *
     * @var string
     */
    protected $priority;

    /**
     * MetaBox context
     *
     * @var string
     */
    protected $context;

    /**
     * MetaBox Builder
     * Used to configure meta-box
     *
     * @var MetaBoxBuilder
     */
    protected $metaBoxBuilder;

    /**
     * Carbon Fields instance
     * Used to builder the metaBox fields
     *
     * @var FieldBuilder
     */
    protected $fieldBuilder;

    /**
     * Base constructor.
     *
     * @param MetaBoxBuilder $metaBoxBuilder
     * @param FieldBuilder $fieldBuilder
     */
    public function __construct(MetaBoxBuilder $metaBoxBuilder, FieldBuilder $fieldBuilder)
    {
        $this->metaBoxBuilder = $metaBoxBuilder;
        $this->fieldBuilder = $fieldBuilder;
    }

    /**
     * Return meta-box settings
     *
     * @param MetaBoxBuilder $metaBoxBuilder
     *
     * @return MetaBoxBuilder
     */
    abstract protected function settings(MetaBoxBuilder $metaBoxBuilder);

    /**
     * Return array of the fields
     *
     * @param FieldBuilder $fieldBuilder
     *
     * @return array
     */
    abstract protected function fields(FieldBuilder $fieldBuilder);

    /**
     * Show the container only on posts from the specified type(s).
     *
     * @param string|PostTypeBase $postType
     *
     * @return $this
     */
    final public function setPostType($postType)
    {
        $this->metaBoxBuilder->setPostType($postType);

        return $this;
    }

    /**
     * Register metaBox and fields using Carbon_Fields hooks
     */
    final public function bind()
    {
        $this->settings($this->metaBoxBuilder);
        add_action('carbon_fields_register_fields', function () {

            $this->metaBoxBuilder->fields(
                $this->fields($this->fieldBuilder)
            );

            $this->metaBoxBuilder->build();
        });
    }
}