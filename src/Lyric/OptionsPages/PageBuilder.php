<?php

namespace Lyric\OptionsPages;

use Carbon_Fields\Container\Container;
use Lyric\Contracts\OptionsPages\PageBuilder as PageBuilderContract;
use Lyric\Support\Strings;

class PageBuilder implements PageBuilderContract
{
    /**
     * Menu title
     * @var string
     */
    protected $title;

    /**
     * Page title
     * @var string
     */
    protected $pageTitle;

    /**
     * Page slug
     * @var string
     */
    protected $slug;

    /**
     * Menu icon
     * @var string
     */
    protected $icon;

    /**
     * Menu position
     * @var float
     */
    protected $position;

    /**
     * Parent page slug
     * @var string
     */
    protected $parent;

    /**
     * Meta box has tabs
     * @var bool
     */
    protected $withTabs = false;

    /**
     * Prefix used to build tab name
     * @var string
     */
    protected $tabNamePrefix;

    /**
     * Fields list
     * @var array
     */
    protected $fields = [];

    /**
     * Set menu title
     *
     * @param $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        if (!is_null($this->slug) || empty($this->slug)) {
            $this->slug($this->title);
        }

        return $this;
    }

    /**
     * Set page title
     *
     * @param $pageTitle
     *
     * @return $this
     */
    public function pageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;

        return $this;
    }

    /**
     * Set slug to use in option page
     *
     * @param $slug
     *
     * @return $this
     */
    public function slug($slug)
    {
        $this->slug = Strings::slug($slug);

        return $this;
    }

    /**
     * Get slug of the page options
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Configure menu icon
     *
     * @param $icon
     *
     * @return $this
     */
    public function icon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Set menu position
     *
     * @param $menuPosition
     *
     * @return $this
     */
    public function position($menuPosition)
    {
        if (!is_numeric($menuPosition)) {
            throw new \InvalidArgumentException('Use numeric position to register option page');
        }

        $this->position = (float)$menuPosition;

        return $this;
    }

    /**
     * Force tabs in meta-box
     *
     * @param string $prefix
     *
     * @return $this
     */
    public function withTabs($prefix = 'Tab')
    {
        $this->withTabs = true;
        $this->tabNamePrefix = $prefix;

        return $this;
    }

    /**
     * Set parent page slug
     *
     * @param $pageParent
     *
     * @return $this
     */
    public function parent($pageParent)
    {
        if ($pageParent instanceof PageBuilderContract) {
            $pageParent = $pageParent->getSlug();
        }

        if ($pageParent instanceof PageBase) {
            $pageParent = $pageParent->getSlug();
        }

        $this->parent = $pageParent;

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
     * @return \Carbon_Fields\Container\Theme_Options_Container
     */
    public function build()
    {
        $this->validateTitle();

        $optionsPageContainer = $this->getCarbonFieldContainer($this->title);

        if (!is_null($this->slug) || !empty($this->slug)) {
            $optionsPageContainer->set_page_file($this->slug);
        }

        if (!is_null($this->pageTitle)) {
            $optionsPageContainer->set_page_menu_title($this->pageTitle);
        }

        if (!is_null($this->position) || !empty($this->position)) {
            $optionsPageContainer->set_page_menu_position($this->position);
        }

        if (!is_null($this->parent) || !empty($this->parent)) {
            $optionsPageContainer->set_page_parent($this->parent);
        }

        if (!is_null($this->icon)) {
            $optionsPageContainer->set_icon($this->icon);
        }

        $optionsPageContainer = $this->addFields($optionsPageContainer);


        return $optionsPageContainer;
    }

    /**
     * Validate if page has title
     * @throws \InvalidArgumentException
     */
    private function validateTitle()
    {
        if (is_null($this->title) || empty($this->title)) {
            throw new \InvalidArgumentException('Configure page title');
        }
    }

    /**
     * Add fields and register tabs if exist
     *
     * @param \Carbon_Fields\Container\Theme_Options_Container|object $optionsPageContainer
     *
     * @return mixed
     */
    protected function addFields($optionsPageContainer)
    {
        if ($this->withTabs) {
            $count = 0;
            foreach ($this->fields as $field) {
                $tabName = sprintf('%1$s %2$s', $this->tabNamePrefix, ++$count);
                $optionsPageContainer->add_tab($tabName, $field);
            }
        } elseif ($this->fieldsHasTabFormat()) {
            foreach ($this->fields as $tab => $field) {
                $optionsPageContainer->add_tab($tab, $field);
            }
        } else {
            $optionsPageContainer->add_fields($this->fields);
        }

        return $optionsPageContainer;
    }

    /**
     * Test if array is associative
     * @return bool
     */
    private function fieldsHasTabFormat()
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
        return Container::factory('theme_options', $title);
    }
}
