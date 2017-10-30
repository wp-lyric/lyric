<?php

namespace Lyric\PostTypes;

trait HasParentPostType
{
    /**
     * Post type name
     *
     * @var string
     */
    protected $postType;

    /**
     * Set post type name
     *
     * @param $postType
     */
    public function setPostType($postType)
    {
        if($postType instanceof \Lyric\Contracts\PostTypes\PostTypeRegister) {
            $postType = $postType->getName();
        }

        if($postType instanceof  \Lyric\Contracts\PostTypes\PostTypeBase) {
            $postType = $postType->getPostTypeName();
        }

        $this->postType = $postType;
    }
}