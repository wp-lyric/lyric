<?php

namespace LyricTests\PostTypes;

use Lyric\PostTypes\Column;
use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;

class ColumnTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown()
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    /**
     * Should register basic columns configurations
     */
    public function testShouldRegisterBasicColumnsConfigurations()
    {
        $column = new Column('post-type', 'Column Title');

        $column->content(function ($postId) {
            return 'This is columns to post with ID: ' . $postId;
        });

        $this->assertAttributeEquals('Column Title', 'title', $column);
        $this->assertAttributeEquals('column-title', 'columnName', $column);
        $this->assertAttributeInternalType('callable', 'callback', $column);
    }

    /**
     * Should register column position
     */
    public function testShouldRegisterColumnPosition()
    {
        $column = new Column('post-type', 'Column');

        $column->before('title');
        $this->assertAttributeEquals('title', 'next', $column);
        $this->assertAttributeEquals('before', 'position', $column);


        $column->after('title');
        $this->assertAttributeEquals('title', 'next', $column);
        $this->assertAttributeEquals('after', 'position', $column);
    }

    /**
     * Set sortable column using meta key
     */
    public function testSetSortableColumnUsingMetaKey()
    {
        $column = new Column('post-type', 'Column');

        $column->sortable('meta-key');

        $this->assertAttributeEquals(true, 'sortable', $column);
        $this->assertAttributeEquals('meta-key', 'metaKey', $column);
    }

    /**
     * Bind should call correctly hooks
     */
    public function testBindShouldCallCorrectlyHooks()
    {
        $column = new Column('post-type', 'Custom Column');

        $column->sortable('meta-key')
            ->after('other-column')
            ->content(function ($postId) {
                return 'This is columns to post with ID: ' . $postId;
            });

        $column->bind();

        $this->assertTrue(has_filter('manage_post-type_posts_columns', 'function ($columns)'));
        $this->assertTrue(has_action('manage_post-type_posts_custom_column', 'function ($column, $postId)'));
        $this->assertTrue(has_filter('manage_edit-post-type_sortable_columns', 'function ($columns)'));
    }

    /**
     * Register complete custom column(
     */
    public function testRegisterCompleteCustomColumn()
    {
        $columnTitles = [
            'title' => 'Column - Post title',
            'other-column' => 'Lyric',
            'date' => 'Today'
        ];

        $columnFilters = [
            'title' => 'Column - Post title',
            'other-column' => 'Lyric',
            'date' => 'Today'
        ];


        Functions\when('add_filter')
            ->alias(function ($filterName, $callable) use (&$columnTitles, &$columnFilters) {
                if ($filterName == 'manage_post-type_posts_columns') {
                    $columnTitles = $callable($columnTitles);
                } else {
                    $columnFilters = $callable($columnTitles);
                }
            });

        Functions\when('add_action')
            ->alias(function ($actionName, $callable) {
                $callable('custom-column', 42);
            });


        $column = new Column('post-type', 'Custom Column');

        $column->sortable('meta-key')
            ->after('other-column')
            ->content(function ($postId) {
                return 'This is columns to post with ID: ' . $postId;
            });

        $column->bind();


        $this->assertEquals([
            'title' => 'Column - Post title',
            'other-column' => 'Lyric',
            'custom-column' => 'Custom Column',
            'date' => 'Today'
        ], $columnTitles);

        $this->assertEquals([
            'title' => 'Column - Post title',
            'other-column' => 'Lyric',
            'date' => 'Today',
            'custom-column' => 'meta-key',
        ], $columnFilters);

        $this->expectOutputString('This is columns to post with ID: 42');
    }

    /**
     * Register column using minimal options
     */
    public function testRegisterColumnUsingMinimalOptions()
    {
        $columnTitles = [
            'title' => 'Column - Post title',
            'other-column' => 'Lyric',
            'date' => 'Today'
        ];

        Functions\when('add_filter')
            ->alias(function ($filterName, $callable) use (&$columnTitles) {
                $columnTitles = $callable($columnTitles);
            });

        Functions\when('add_action')
            ->alias(function ($actionName, $callable) {
                $callable('custom-column', 42.2);
            });


        $column = new Column('post-type', 'lyric-column', 'Lyric Data');
        $column->bind();

        $this->assertEquals([
            'title' => 'Column - Post title',
            'other-column' => 'Lyric',
            'date' => 'Today',
            'lyric-column' => 'Lyric Data',
        ], $columnTitles);
    }

    /**
     * Remove column of the post type admin
     */
    public function testRemoveColumnOfThePostTypeAdmin()
    {
        // Settings
        $columnTitles = [
            'title' => 'Column - Post title',
            'other-column' => 'Lyric',
            'date' => 'Today'
        ];

        Functions\when('add_filter')
            ->alias(function ($filterName, $callable) use (&$columnTitles) {
                $columnTitles = $callable($columnTitles);
            });

        // Remove column
        $column = new Column('post-type', 'title');
        $column->remove()->bind();

        // Do asserts
        $this->assertAttributeEquals(true, 'removeColumn', $column);
        $this->assertEquals([
            'other-column' => 'Lyric',
            'date' => 'Today',
        ], $columnTitles);
    }
}
