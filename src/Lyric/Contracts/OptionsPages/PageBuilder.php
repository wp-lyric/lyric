<?php

namespace Lyric\Contracts\OptionsPages;

interface PageBuilder
{
    /**
     * Set menu title
     *
     * @param $title
     *
     * @return $this
     */
    public function title($title);

    /**
     * Set page title
     *
     * @param $pageTitle
     *
     * @return $this
     */
    public function pageTitle($pageTitle);

    /**
     * Set slug to use in option page
     *
     * @param $slug
     *
     * @return $this
     */
    public function slug($slug);

    /**
     * Get slug of the page options
     *
     * @return string
     */
    public function getSlug();

    /**
     * Configure menu icon
     *
     * @param $icon
     *
     * @return $this
     */
    public function icon($icon);

    /**
     * Set menu position
     *
     * @param $menuPosition
     *
     * @return $this
     */
    public function position($menuPosition);

    /**
     * Set parent page slug
     *
     * @param $parent
     *
     * @return $this
     */
    public function parent($parent);

    /**
     * Array of the fields using Carbon_Fields\Field\Field
     *
     * @param array $fields
     */
    public function fields(array $fields);

    /**
     * Build Options Page using Carbon_Fields
     *
     * @return \Carbon_Fields\Container\Theme_Options_Container
     */
    public function build();
}