<?php

namespace Lyric\Contracts\PostTypes;

use Lyric\Hooks\BindToWordPress;

interface PostTypeRegister extends BindToWordPress
{
    /**
     * Assign the post type names.
     * Accepts post type: name, slug, plural, singular.
     *
     * @param mixed $names a post type name as string or an array of names
     */
    public function assignNames($names);

    /**
     * Return post type name
     *
     * @return string
     */
    public function getName();

    /**
     * Set the post type labels.
     *
     * @param array $labels an array of post type options
     *
     * @return $this
     */
    public function labels(array $labels);

    /**
     * Set the post type options.
     *
     * @param array $options an array of post type options
     *
     * @return $this
     */
    public function options(array $options);

    /**
     * Set post type slug
     *
     * @param string $slug
     *
     * @return $this
     */
    public function slug($slug);

    /**
     * Set post type icon
     *
     * @param string $slug
     *
     * @return $this
     */
    public function icon($icon);
}
