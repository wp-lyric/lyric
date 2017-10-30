<?php

namespace Lyric\Contracts\PostTypes;

interface ColumnsFactory
{
    /**
     * ColumnsFactory constructor.
     */
    public function __construct($postType);

    /**
     * Add new column and return Column instance
     *
     * @param $title
     * @param null $id
     *
     * @return \Lyric\PostTypes\Column
     */
    public function addColumn($title, $id = null);

    /**
     * Remove column
     *
     * @param $id
     *
     * @return \Lyric\PostTypes\Column
     */
    public function removeColumn($id);

    /**
     * Bind all registered columns to WordPress
     */
    public function bind();
}