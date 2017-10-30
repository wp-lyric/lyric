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

    public function test_should_register_basic_columns_configurations()
    {
        $column = new Column('post-type', 'Column Title');

        $column->content(function ($postId) {
            return 'This is columns to post with ID: ' . $postId;
        });

        $this->assertAttributeEquals('Column Title', 'title', $column);
        $this->assertAttributeEquals('column-title', 'columnId', $column);
        $this->assertAttributeInternalType('callable', 'callback', $column);
    }

    public function test_should_register_column_position()
    {
        $column = new Column('post-type', 'Column');

        $column->before('title');
        $this->assertAttributeEquals('title', 'next', $column);
        $this->assertAttributeEquals('before', 'position', $column);


        $column->after('title');
        $this->assertAttributeEquals('title', 'next', $column);
        $this->assertAttributeEquals('after', 'position', $column);
    }

    public function test_set_sortable_column_using_meta_key()
    {
        $column = new Column('post-type', 'Column');

        $column->sortable('meta-key');

        $this->assertAttributeEquals(true, 'sortable', $column);
        $this->assertAttributeEquals('meta-key', 'metaKey', $column);
    }

    public function test_bind_should_call_correctly_hooks()
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

    public function test_register_complete_custom_column()
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

    public function test_register_column_using_minimal_options()
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

    public function test_remove_column_of_the_post_type_admin()
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
