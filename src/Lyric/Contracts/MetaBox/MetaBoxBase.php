<?php

namespace Lyric\Contracts\MetaBox;

interface MetaBoxBase
{
    /**
     * Show the container only on posts from the specified type(s).
     *
     * @param string|\Lyric\Contracts\PostTypes\PostTypeBase $postType
     *
     * @return $this
     */
    public function setPostType($postType);

    /**
     * Register metaBox and fields using Carbon_Fields hooks
     */
    public function bind();
}