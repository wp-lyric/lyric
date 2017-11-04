<?php

namespace Lyric\MetaBox;

use Lyric\Contracts\MetaBox\MetaBoxBuilder as MetaBoxBuilderContract;
use Carbon_Fields\Container\Container;
use Lyric\PostTypes\HasParentPostType;

class MetaBoxBuilder implements MetaBoxBuilderContract
{
    use HasParentPostType;

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
     * Meta box has tabs
     *
     * @var bool
     */
    protected $withTabs = false;

    /**
     * Prefix used to build tab name
     *
     * @var string
     */
    protected $tabNamePrefix;

    /**
     * Carbon Fields instance
     * Used to builder the metaBox fields
     *
     * @var array
     */
    protected $fields = [];

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
     * Force tabs in meta-box
     *
     * @param $hasTabs
     *
     * @return $this
     */
    public function withTabs($hasTabs, $prefix = 'Tab')
    {
        $this->withTabs = $hasTabs;
        $this->tabNamePrefix = $prefix;

        return $this;
    }

    /**
     * Array of the fields using Carbon_Fields\Field\Field
     *
     * @param array $fields
     *
     * @return $this
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
        if (is_null($this->title) || empty($this->title)) {
            throw new \InvalidArgumentException('Configure Meta Box title');
        }

        $postMetaContainer = $this->getCarbonFieldContainer($this->title);

        if (!is_null($this->postType)) {
            $postMetaContainer->show_on_post_type($this->postType);
        }

        if (!is_null($this->priority)) {
            $postMetaContainer->set_priority($this->priority);
        }

        if (!is_null($this->context)) {
            $postMetaContainer->set_context($this->context);
        }

        // Configure fields
        if ($this->withTabs) {
            $count = 0;
            foreach ($this->fields as $field) {
                $tabName = sprintf('%1$s %2$s', $this->tabNamePrefix, ++$count);
                $postMetaContainer->add_tab($tabName, $field);
            }

        } elseif ($this->fieldsHasTabs()) {
            foreach ($this->fields as $tab => $field) {
                $postMetaContainer->add_tab($tab, $field);
            }

        } else {
            $postMetaContainer->add_fields($this->fields);
        }


        return $postMetaContainer;
    }

    /**
     * Test if array is associative
     *
     * @return bool
     */
    private function fieldsHasTabs()
    {
        if ([] === $this->fields) {
            return false;
        };

        return array_keys($this->fields) !== range(0, count($this->fields) - 1);
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