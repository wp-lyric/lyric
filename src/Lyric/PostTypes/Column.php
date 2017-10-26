<?php

namespace Lyric\PostTypes;

use Lyric\Support\Strings;

class Column
{
    /**
     * Post types where columns are displayed
     *
     * @var array
     */
    protected $postType;

    /**
     * Column title
     *
     * @var string
     */
    protected $title;

    /**
     * Column identify
     *
     * @var string
     */
    protected $columnId;

    /**
     * Callback used to render column content
     *
     * @var callable
     */
    protected $callback;

    /**
     * Insert custom column in determined position
     * Supported after|before
     *
     * @var string
     */
    protected $position;

    /**
     * Insert custom column next to this
     *
     * @var string
     */
    protected $next;

    /**
     * This is sortable column
     *
     * @var bool
     */
    protected $sortable = false;

    /**
     * Meta key used to sort column
     *
     * @var string
     */
    protected $metaKey;

    /**
     * Column constructor.
     *
     * @param $postType
     * @param $tittle
     */
    public function __construct($postType, $id, $title = null)
    {
        $this->postType = $postType;

        if (is_null($title)) {
            $title = $id;
            $id = Strings::slug($title);
        }

        $this->title = $title;
        $this->columnId = $id;
    }

    /**
     * Register column content using callback
     *
     * @param $callback
     *
     * @return $this
     */
    public function content($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Insert column before specified column
     *
     * @param string $columnId
     *
     * @return $this
     */
    public function before($columnId)
    {
        $this->position($columnId, 'before');

        return $this;
    }

    /**
     * Insert column after specified column
     *
     * @param string $columnId
     *
     * @return $this
     */
    public function after($columnId)
    {
        $this->position($columnId, 'after');

        return $this;
    }

    public function position($next, $position)
    {
        $this->next = $next;
        $this->position = $position;

        return $this;
    }

    /**
     * Configure column as sortable
     *
     * @param $metaKey
     *
     * @return $this
     */
    public function sortable($metaKey)
    {
        $this->sortable = true;
        $this->metaKey = $metaKey;

        return $this;
    }

    /**
     * Bind columns to WordPress
     */
    public function bind()
    {
        add_filter("manage_{$this->postType}_posts_columns", function ($columns) {
            $columns = $this->resolveColumns($columns, $this->columnId, $this->title, $this->next, $this->position);

            return $columns;
        });

        add_action("manage_{$this->postType}_posts_custom_column", function ($column, $postId) {
            if ($column == $this->columnId) {
                $callback = $this->callback;
                if (is_callable($callback)) {
                    echo $callback($postId);
                }
            }
        }, 10, 2);

        if ($this->sortable) {
            add_filter("manage_edit-{$this->postType}_sortable_columns", function ($columns) {
                $columns[$this->columnId] = $this->metaKey;

                return $columns;
            });
        }

    }

    /**
     * Include new item in the columns list
     *
     * @param $columns
     * @param $newItemId
     * @param $newItemTitle
     * @param $nextItemId
     * @param string $position
     *
     * @return array
     */
    protected function resolveColumns($columns, $newItemId, $newItemTitle, $nextItemId, $position = 'after')
    {
        $keyPosition = array_search($nextItemId, array_keys($columns));

        if ('after' == $position) {
            $keyPosition++;
        }

        if (!$keyPosition) {
            return array_merge($columns, [$newItemId => $newItemTitle]);
        }

        $result = array_slice($columns, 0, $keyPosition);
        $result = array_merge($result, [$newItemId => $newItemTitle]);
        $result = array_merge($result, array_slice($columns, $keyPosition));

        return $result;
    }
}