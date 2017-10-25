<?php

namespace Lyric\MetaBox;

use Lyric\Contracts\MetaBox\MetaBoxBuilder as MetaBoxBuilderContract;
use Lyric\Contracts\PostTypes\PostTypeBase;
use Carbon_Fields\Container\Container;

class MetaBoxBuilder implements MetaBoxBuilderContract
{
    /**
     * Post type name
     *
     * @var string|PostTypeBase
     */
    protected $postType;

    /**
     * MetaBox title
     *
     * @var string
     */
    protected $title;

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
     * Carbon Fields instance
     * Used to builder the metaBox fields
     *
     * @var array
     */
    protected $fields;

    /**
     * Set MetaBox title
     *
     * @param $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Sets the meta box container context
     *
     * @param $priority
     *
     * @return $this
     */
    public function priority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Sets the meta box container priority
     *
     * @param $context
     *
     * @return $this
     */
    public function context($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Show the container only on posts from the specified type(s).
     *
     * @param string|PostTypeBase $postType
     *
     * @return $this
     */
    public function setPostType($postType)
    {
        $this->postType = ($postType instanceof PostTypeBase) ? $postType->postTypeName() : $postType;

        return $this;
    }

    /**
     * Array of the fields using Carbon_Fields\Field\Field
     *
     * @param array $fields
     */
    public function fields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }


    /**
     * Build Options Page using Carbon_Fields
     *
     * @return \Carbon_Fields\Container\Theme_Options_Container
     */
    public function build()
    {
        if(is_null($this->title) || empty($this->title)) {
            throw new \InvalidArgumentException('Configure Meta Box title');
        }

        $postMetaContainer = $this->getCarbonFieldContainer($this->title);

        if (!is_null($this->postType)) {
            $postMetaContainer->show_on_post_type($this->postType);
        }

        if(!is_null($this->priority)) {
            $postMetaContainer->set_priority($this->priority);
        }

        if(!is_null($this->context)) {
            $postMetaContainer->set_context($this->context);
        }

        $postMetaContainer->add_fields($this->fields);

        return $postMetaContainer;
    }


    /**
     * Get Carbon Field Container
     *
     * @param $title
     *
     * @return \Carbon_Fields\Container\Theme_Options_Container|object
     */
    protected function getCarbonFieldContainer($title)
    {
        return Container::factory('post_meta', $title);
    }
}