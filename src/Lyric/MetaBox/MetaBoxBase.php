<?php

namespace Lyric\MetaBox;

use Lyric\Contracts\MetaBox\MetaBoxBuilder;
use Lyric\Contracts\Fields\FieldFactory;
use Lyric\Contracts\PostTypes\PostTypeBase;
use Lyric\Hooks\BindToWordPress;

abstract class MetaBoxBase implements BindToWordPress
{
    /**
     * Post type name
     * @var string|PostTypeBase
     */
    protected $postType;

    /**
     * MetaBox priority
     * @var string
     */
    protected $priority;

    /**
     * MetaBox context
     * @var string
     */
    protected $context;

    /**
     * MetaBox Builder
     * Used to configure meta-box
     * @var MetaBoxBuilder
     */
    protected $metaBoxBuilder;

    /**
     * Carbon Fields instance
     * Used to builder the metaBox fields
     * @var FieldFactory
     */
    protected $fieldFactory;

    /**
     * Base constructor.
     *
     * @param MetaBoxBuilder $metaBoxBuilder
     * @param FieldFactory   $fieldFactory
     */
    public function __construct(MetaBoxBuilder $metaBoxBuilder, FieldFactory $fieldFactory)
    {
        $this->metaBoxBuilder = $metaBoxBuilder;
        $this->fieldFactory = $fieldFactory;
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
     * @param FieldFactory $fieldFactory
     *
     * @return array
     */
    abstract protected function fields(FieldFactory $fieldFactory);

    /**
     * Register metaBox and fields using Carbon_Fields hooks
     */
    public function bind()
    {
        $this->settings($this->metaBoxBuilder);
        add_action('carbon_fields_register_fields', function () {

            $this->metaBoxBuilder->fields(
                $this->fields($this->fieldFactory)
            );

            $this->metaBoxBuilder->build();
        });
    }
}
