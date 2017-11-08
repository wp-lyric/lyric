<?php

namespace Lyric\PostTypes;

use Lyric\Contracts\PostTypes\ColumnsFactory as ColumnsFactoryContract;

class ColumnsFactory implements ColumnsFactoryContract
{
    /**
     * Post type name
     * @var string
     */
    protected $postType;

    /**
     * List of the columns
     * @var array
     */
    protected $columns = [];

    /**
     * ColumnsFactory constructor.
     *
     * @param string $postType
     */
    public function __construct($postType)
    {
        $this->postType = $postType;
    }

    /**
     * Add new column and return Column instance
     *
     * @param string      $columnTitle
     * @param string|null $columnName
     *
     * @return Column
     */
    public function addColumn($columnTitle, $columnName = null)
    {
        if (!is_null($columnName)) {
            $column = new Column($this->postType, $columnName, $columnTitle);
        } else {
            $column = new Column($this->postType, $columnTitle);
        }

        $this->columns[] = $column;

        return $column;
    }

    /**
     * Remove column
     *
     * @param string $columnName
     *
     * @return Column
     */
    public function removeColumn($columnName)
    {
        $column = $this->addColumn('', $columnName);
        $column->remove();

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
