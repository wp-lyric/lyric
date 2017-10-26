<?php

namespace Lyric\PostTypes;

use Lyric\Contracts\PostTypes\ColumnsFactory as ColumnsFactoryContract;

class ColumnsFactory implements ColumnsFactoryContract
{
    /**
     * Post type name
     *
     * @var string
     */
    protected $postType;

    /**
     * List of the columns
     *
     * @var array
     */
    protected $columns = [];

    /**
     * ColumnsFactory constructor.
     */
    public function __construct($postType)
    {
        $this->postType = $postType;
    }

    /**
     * Add new column and return Column instance
     *
     * @param $title
     * @param null $id
     *
     * @return Column
     */
    public function addColumn($title, $id = null)
    {
        if (!is_null($id)) {
            $column = new Column($this->postType, $id, $title);
        } else {
            $column = new Column($this->postType, $title);
        }

        $this->columns[] = $column;

        return $column;
    }

    /**
     * Bind all registered columns to WordPress
     */
    public function bind()
    {
        foreach ($this->columns as $column) {
            $column->bind();
        }
    }
}