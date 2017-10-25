<?php

namespace Lyric\Contracts\MetaBox;

interface MetaBoxBuilder
{
    /**
     * Set MetaBox title
     *
     * @param $title
     *
     * @return $this
     */
    public function title($title);

    /**
     * Sets the meta box container context
     *
     * @param $priority
     *
     * @return $this
     */
    public function priority($priority);

    /**
     * Sets the meta box container priority
     *
     * @param $context
     *
     * @return $this
     */
    public function context($context);

    /**
     * Show the container only on posts from the specified type(s).
     *
     * @param string|\Lyric\Contracts\PostTypes\PostTypeBase $postType
     *
     * @return $this
     */
    public function setPostType($postType);

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