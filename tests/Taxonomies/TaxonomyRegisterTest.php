<?php

namespace LyricTests\Taxonomies;

use Lyric\Taxonomies\TaxonomyRegister;
use PHPUnit\Framework\TestCase;
use Mockery;
use Brain\Monkey;
use Brain\Monkey\Functions;

class TaxonomyRegisterTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown()
    {
        Monkey\tearDown();
        Mockery::close();
        parent::tearDown();
    }

    public function test_should_register_taxonomy_using_string_name()
    {
        $taxonomy = new TaxonomyRegister('Tag', 'lyric-post-type');


        $this->assertAttributeEquals('tags', 'taxonomyName', $taxonomy);
        $this->assertAttributeEquals('Tag', 'singular', $taxonomy);
        $this->assertAttributeEquals('Tags', 'plural', $taxonomy);
        $this->assertAttributeEquals('tags', 'slug', $taxonomy);
        $this->assertAttributeEquals('lyric-post-type', 'postType', $taxonomy);
    }

    public function test_should_register_taxonomy_using_array_with_names()
    {
        $taxonomy = new TaxonomyRegister(
            [
                'name' => 'lyric-tax',
                'singular' => 'Lyric Tax',
                'plural' => 'Taxonomies',
                'slug' => 'l-tax'
            ],
            'lyric-post-type'
        );

        $this->assertAttributeEquals('lyric-tax', 'taxonomyName', $taxonomy);
        $this->assertAttributeEquals('Lyric Tax', 'singular', $taxonomy);
        $this->assertAttributeEquals('Taxonomies', 'plural', $taxonomy);
        $this->assertAttributeEquals('l-tax', 'slug', $taxonomy);
        $this->assertAttributeEquals('lyric-post-type', 'postType', $taxonomy);
    }

    public function test_register_taxonomy_using_minimal_array_of_the_names()
    {
        $taxonomy = new TaxonomyRegister(['name' => 'tag'], 'lyric-post-type');

        $this->assertAttributeEquals('tag', 'taxonomyName', $taxonomy);
        $this->assertAttributeEquals('Tag', 'singular', $taxonomy);
        $this->assertAttributeEquals('Tags', 'plural', $taxonomy);
        $this->assertAttributeEquals('tag', 'slug', $taxonomy);
        $this->assertAttributeEquals('lyric-post-type', 'postType', $taxonomy);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Use valid arguments(string|array) to define taxonomy name
     */
    public function test_throw_exception_if_used_not_is_array_or_string()
    {
        $taxonomy = new TaxonomyRegister(null, 'lyric-post');
        $taxonomy->assignNames(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The [name] key is required in Lyric\Taxonomies\TaxonomyRegister::assignNames
     */
    public function test_throw_exception_if_use_names_array_not_have_required_argument()
    {
        $taxonomy = new TaxonomyRegister(['singular' => 'Tag'], 'lyric-post');
    }

    public function test_should_register_taxonomy_using_post_type_object()
    {
        $register = Mockery::mock(\Lyric\Contracts\PostTypes\RegisterPostType::class);

        $register->shouldReceive('getName')
            ->once()
            ->withNoArgs()
            ->andReturn('lyric-post-type');


        $taxonomy = new TaxonomyRegister('Tag', $register);

        $this->assertAttributeEquals('lyric-post-type', 'postType', $taxonomy);
    }

    public function test_add_labels_and_return_merged_with_default_labels()
    {
        // Mock functions
        Functions\stubs(
            [
                '__',
                '_x',
            ]
        );


        // Execute register
        $taxonomy = new TaxonomyRegister('Tag', 'lyric-post-type');
        $taxonomy->labels([
            'all_items' => 'All Tags',
            'add_new_item' => 'Add New Tag',
        ]);


        // Asserts
        $this->assertEquals([
            'name' => 'Tags',
            'singular_name' => 'Tag',
            'menu_name' => 'Tags',
            'all_items' => 'All Tags',
            'add_new_item' => 'Add New Tag',
        ],
            $taxonomy->getLabels()
        );

        $this->assertAttributeEquals([
            'all_items' => 'All Tags',
            'add_new_item' => 'Add New Tag',
        ],
            'labels',
            $taxonomy
        );
    }

    public function test_set_slug_without_apply_format()
    {
        $taxonomy = new TaxonomyRegister('lyric-taxonomy', 'lyric-post');
        $taxonomy->slug('taxSlug', false);

        $this->assertAttributeEquals('taxSlug', 'slug', $taxonomy);
    }

    public function test_add_options_and_return_merged_with_default_options()
    {
        // Mock functions
        Functions\stubs(
            [
                '__',
                '_x',
            ]
        );


        // Execute register
        $taxonomy = new TaxonomyRegister('Tag', 'lyric-post-type');
        $taxonomy->options([
            'show_in_rest' => true
        ]);


        // Asserts
        $this->assertEquals([
            'labels' => [
                'name' => 'Tags',
                'singular_name' => 'Tag',
                'menu_name' => 'Tags',
            ],
            'public' => true,
            'show_in_rest' => true,
            'rewrite' => [
                'slug' => 'tags'
            ]
        ],
            $taxonomy->getOptions()
        );

        $this->assertAttributeEquals([
            'show_in_rest' => true
        ],
            'options',
            $taxonomy
        );
    }

    public function test_bind_taxonomy_to_wordpress()
    {
        // Mock functions
        Functions\stubs(
            [
                '__',
                '_x',
            ]
        );

        $hookName = null;
        $hookPriority = null;
        Functions\when('add_action')->alias(function ($action, $callback, $priority) use (&$hookName, &$hookPriority) {
            $hookName = $action;
            $hookPriority = $priority;
            $callback();
        });

        Functions\expect('taxonomy_exists')->once()->with('tags')->andReturn(false);
        Functions\expect('register_taxonomy')
            ->once()
            ->with('tags', 'lyric-post-type', [
                'labels' => [
                    'name' => 'Tags',
                    'singular_name' => 'Tag',
                    'menu_name' => 'Tags',
                ],
                'public' => true,
                'rewrite' => [
                    'slug' => 'tags'
                ]
            ]);

        // Execute register
        $taxonomy = new TaxonomyRegister('Tag', 'lyric-post-type');
        $taxonomy->bind();

        // Assertions
        $this->assertEquals('init', $hookName);
        $this->assertEquals(1, $hookPriority);
    }
}