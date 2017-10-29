<?php

namespace Lyric\Contracts\Taxonomies;

interface TaxonomyRegister
{
    /**
     * Assign the post type names.
     * Accepts post type: name, slug, plural, singular.
     *
     * @param mixed $names Post type name as string or an array of names
     *
     * @return $this
     */
    public function assignNames($names);

    /**
     * Save post type used to display taxonomy
     *
     * @param $postType
     *
     * @return $this
     */
    public function setPostType($postType);

    /**
     * Return taxonomy name
     *
     * @return string
     */
    public function getName();

    /**
     * Set post type slug
     *
     * @param string $slug
     *
     * @return $this
     */
    public function slug($slug);

    /**
     * Set the post type labels.
     *
     * @param array $labels An array of post type options
     */
    public function labels(array $labels);

    /**
     * Get labels list using configured labels and default values
     *
     * @return array
     */
    public function getLabels();

    /**
     * Set the post type options.
     *
     * @param array $options an array of post type options
     */
    public function options(array $options);

    /**
     * Get options list using configured labels and default values
     *
     * @return array
     */
    public function getOptions();

    /**
     * Bind methods to WordPress actions and filters.
     */
    public function bind();
}