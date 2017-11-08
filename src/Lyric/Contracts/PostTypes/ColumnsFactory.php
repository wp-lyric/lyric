<?php

namespace Lyric\Contracts\PostTypes;

use Lyric\Hooks\BindToWordPress;

interface ColumnsFactory extends BindToWordPress
{
    /**
     * ColumnsFactory constructor.
     */
    public function __construct($postType);

    /**
     * Add new column and return Column instance
     *
     * @param string      $columnTitle
     * @param string|null $columnName
     *
     * @return \Lyric\PostTypes\Column
     */
    public function addColumn($columnTitle, $columnName = null);

    /**
     * Remove column
     *
     * @param string $columnName
     *
     * @return \Lyric\PostTypes\Column
     */
    public function removeColumn($columnName);
}
