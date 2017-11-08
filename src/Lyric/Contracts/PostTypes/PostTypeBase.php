<?php

namespace Lyric\Contracts\PostTypes;

use Lyric\Hooks\BindToWordPress;

interface PostTypeBase extends BindToWordPress
{
    /**
     * Return post type name
     *
     * @return string
     */
    public function getPostTypeName();
}
