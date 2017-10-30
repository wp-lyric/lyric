<?php

namespace Lyric\Contracts\PostTypes;

interface PostTypeBase
{
    /**
     * Return post type name
     *
     * @return string
     */
    public function getPostTypeName();

    /**
     * Bind Post Type to WordPress
     */
    public function bind();
}