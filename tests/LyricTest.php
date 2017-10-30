<?php

namespace LyricTests;

use PHPUnit\Framework\TestCase;
use Mockery;
use Brain\Monkey;
use Brain\Monkey\Actions;
use Lyric\Lyric;
use League\Container\Container;
use Lyric\PostTypes\PostTypeBase;
use Lyric\OptionsPages\PageBase;

class LyricTest extends TestCase
{
    public function setUp()
    {
        Monkey\setUp();
    }

    public function tearDown()
    {
        Monkey\tearDown();
        Mockery::close();
    }

    /**
     * @return \Mockery\MockInterface
     */
    protected function get_mock_container()
    {
        $container = Mockery::mock(Container::class);

        $container->shouldReceive('add')
            ->once()
            ->with(\Lyric\Contracts\PostTypes\PostTypeRegister::class, \Lyric\PostTypes\PostTypeRegister::class)
            ->andReturnSelf();

        $container->shouldReceive('add')
            ->once()
            ->with(\Lyric\Contracts\MetaBox\MetaBoxBuilder::class, \Lyric\MetaBox\MetaBoxBuilder::class)
            ->andReturnSelf();

        $container->shouldReceive('add')
            ->once()
            ->with(\Lyric\Contracts\MetaBox\MetaBoxFactory::class, \Lyric\MetaBox\MetaBoxFactory::class)
            ->andReturnSelf();

        $container->shouldReceive('add')
            ->once()
            ->with(\Lyric\Contracts\PostTypes\ColumnsFactory::class, \Lyric\PostTypes\ColumnsFactory::class)
            ->andReturnSelf();

        $container->shouldReceive('add')
            ->once()
            ->with(\Lyric\Contracts\Taxonomies\TaxonomyRegister::class, \Lyric\Taxonomies\TaxonomyRegister::class)
            ->andReturnSelf();

        $container->shouldReceive('add')
            ->once()
            ->with(\Lyric\Contracts\Taxonomies\TaxonomyFactory::class, \Lyric\Taxonomies\TaxonomyFactory::class)
            ->andReturnSelf();

        $container->shouldReceive('add')
            ->once()
            ->with(\Lyric\Contracts\OptionsPages\PageBuilder::class, \Lyric\OptionsPages\PageBuilder::class)
            ->andReturnSelf();

        $container->shouldReceive('share')
            ->once()
            ->with(\Lyric\Contracts\Fields\FieldFactory::class, \Lyric\Fields\FieldFactory::class)
            ->andReturnSelf();

        return $container;
    }

    public function test_get_lyric_instance()
    {
        $lyric = Lyric::make();

        $this->assertInstanceOf(Lyric::class, $lyric);
        $this->assertEquals($lyric, Lyric::make());
    }

    public function test_should_register_container()
    {
        $container = $this->get_mock_container();
        $lyric = new Lyric($container);

        $this->assertAttributeInstanceOf(Container::class, 'container', $lyric);
        $this->assertInstanceOf(Container::class, $lyric->container());
    }

    public function test_should_register_post_types()
    {
        $postType = Mockery::mock(PostTypeBase::class);
        $container = $this->get_mock_container();

        // Configure mocks
        $container->shouldReceive('share')
            ->once()
            ->with(PostTypeBase::class)
            ->andReturnSelf();

        $container->shouldReceive('withArgument')
            ->once()
            ->with($container)
            ->andReturnSelf();

        $container->shouldReceive('has')
            ->once()
            ->with(PostTypeBase::class)
            ->andReturn($postType);

        $container->shouldReceive('get')
            ->times(3)
            ->with(PostTypeBase::class)
            ->andReturn($postType);

        $postType->shouldReceive('postTypeName')
            ->once()
            ->withNoArgs()
            ->andReturn('lyric-post-type');


        $container->shouldReceive('has')
            ->once()
            ->with('FakePostType')
            ->andReturn(false);

        $lyric = new Lyric($container);
        $lyric->addPostType(PostTypeBase::class);


        // Assertions
        $this->assertAttributeEquals(['lyric-post-type' => PostTypeBase::class], 'postTypeList', $lyric);
        $this->assertEquals($postType, $lyric->postType('lyric-post-type'));
        $this->assertEquals($postType, $lyric->postType(PostTypeBase::class));
        $this->assertFalse($lyric->postType('FakePostType'));
    }

    public function test_save_taxonomies_to_bind_in_lyric_boot()
    {
        $container = $this->get_mock_container();

        $taxonomyRegister = Mockery::mock(\Lyric\Contracts\Taxonomies\TaxonomyRegister::class);

        // Configure mocks
        $container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\Taxonomies\TaxonomyRegister::class)
            ->andReturn($taxonomyRegister);

        $container->shouldReceive('share')
            ->once()
            ->with(\Lyric\Taxonomies\TaxonomyBase::class)
            ->andReturnSelf();

        $container->shouldReceive('withArgument')
            ->once()
            ->with($taxonomyRegister)
            ->andReturnSelf();

        $container->shouldReceive('withArgument')
            ->once()
            ->with(\Lyric\Contracts\Fields\FieldFactory::class)
            ->andReturnSelf();

        $taxonomyRegister->shouldReceive('setPostType')
            ->once()
            ->with('lyric-post-type')
            ->andReturnSelf();

        // Execute
        $lyric = new Lyric($container);
        $lyric->addTaxonomy(\Lyric\Taxonomies\TaxonomyBase::class, 'lyric-post-type');

        // Assertions
        $this->assertAttributeContains(\Lyric\Taxonomies\TaxonomyBase::class, 'taxonomiesList', $lyric);
    }

    public function test_add_meta_box_and_save_to_bind_in_boot()
    {
        $container = $this->get_mock_container();

        $metaBoxBuilder = Mockery::mock(\Lyric\Contracts\MetaBox\MetaBoxBuilder::class);

        // Configure mocks
        $container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\MetaBox\MetaBoxBuilder::class)
            ->andReturn($metaBoxBuilder);

        $container->shouldReceive('share')
            ->once()
            ->with(\Lyric\Contracts\MetaBox\MetaBoxBase::class)
            ->andReturnSelf();

        $container->shouldReceive('withArgument')
            ->once()
            ->with($metaBoxBuilder)
            ->andReturnSelf();

        $container->shouldReceive('withArgument')
            ->once()
            ->with(\Lyric\Contracts\Fields\FieldFactory::class)
            ->andReturnSelf();

        $metaBoxBuilder->shouldReceive('setPostType')
            ->once()
            ->with('lyric-post-type')
            ->andReturnSelf();
        // Execute
        $lyric = new Lyric($container);
        $lyric->addMetaBox(\Lyric\Contracts\MetaBox\MetaBoxBase::class, 'lyric-post-type');

        // Assertions
        $this->assertAttributeContains(
            \Lyric\Contracts\MetaBox\MetaBoxBase::class,
            'metaBoxesList',
            $lyric
        );
    }

    public function test_should_register_options_page()
    {
        $container = $this->get_mock_container();

        // Configure mocks
        $container->shouldReceive('share')
            ->once()
            ->with(PageBase::class)
            ->andReturnSelf();

        $container->shouldReceive('withArgument')
            ->once()
            ->with(\Lyric\Contracts\OptionsPages\PageBuilder::class)
            ->andReturnSelf();

        $container->shouldReceive('withArgument')
            ->once()
            ->with(\Lyric\Contracts\Fields\FieldFactory::class)
            ->andReturnSelf();

        $lyric = new Lyric($container);
        $lyric->addOptionsPage(PageBase::class);


        // Assertions
        $this->assertAttributeContains(PageBase::class, 'optionsPageList', $lyric);
    }

    public function test_bind_post_type_to_wordpress()
    {
        $container = $this->get_mock_container();
        // Use contact to create full mock
        $postType = Mockery::mock(\Lyric\Contracts\PostTypes\PostTypeBase::class);
        $taxonomy = Mockery::mock('TaxonomyBase'); // Used fake name to prevent mockery from call original class
        $metaBox = Mockery::mock(\Lyric\Contracts\MetaBox\MetaBoxBase::class);
        $optionsPage = Mockery::mock(\Lyric\Contracts\OptionsPages\PageBase::class);

        // Configure mocks

        // Bind post type
        $container->shouldReceive('has')
            ->once()
            ->with(\Lyric\Contracts\PostTypes\PostTypeBase::class)
            ->andReturn(true);

        $container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\PostTypes\PostTypeBase::class)
            ->andReturn($postType);

        $postType->shouldReceive('bind')
            ->once()
            ->withNoArgs();

        // Bind Taxonomies
        $container->shouldReceive('has')
            ->once()
            ->with(\Lyric\Taxonomies\TaxonomyBase::class)
            ->andReturn(true);

        $container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Taxonomies\TaxonomyBase::class)
            ->andReturn($taxonomy);

        $taxonomy->shouldReceive('bind')
            ->once()
            ->withNoArgs();

        // Bind Meta Boxes
        $container->shouldReceive('has')
            ->once()
            ->with(\Lyric\Contracts\MetaBox\MetaBoxBase::class)
            ->andReturn(true);

        $container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\MetaBox\MetaBoxBase::class)
            ->andReturn($metaBox);

        $metaBox->shouldReceive('bind')
            ->once()
            ->withNoArgs();

        // Bind Options Page
        $container->shouldReceive('has')
            ->once()
            ->with(\Lyric\Contracts\OptionsPages\PageBase::class)
            ->andReturn(true);

        $container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\OptionsPages\PageBase::class)
            ->andReturn($optionsPage);

        $optionsPage->shouldReceive('bind')
            ->once()
            ->withNoArgs();


        $actions = null;


        // configure Lyric
        $lyric = new Lyric($container);

        $reflectLyric = new \ReflectionClass(Lyric::class);

        // Add post type in property list
        $postTypeListProperty = $reflectLyric->getProperty('postTypeList');
        $postTypeListProperty->setAccessible(true);
        $postTypeListProperty->setValue($lyric, [
            'lyric-post-type' => \Lyric\Contracts\PostTypes\PostTypeBase::class
        ]);

        // Add meta box list
        $taxonomiesListProperty = $reflectLyric->getProperty('taxonomiesList');
        $taxonomiesListProperty->setAccessible(true);
        $taxonomiesListProperty->setValue($lyric, [
            \Lyric\Taxonomies\TaxonomyBase::class
        ]);

        // Add meta box list
        $metaBoxListProperty = $reflectLyric->getProperty('metaBoxesList');
        $metaBoxListProperty->setAccessible(true);
        $metaBoxListProperty->setValue($lyric, [
            \Lyric\Contracts\MetaBox\MetaBoxBase::class
        ]);

        // Add options page in list
        $optionsPageListProperty = $reflectLyric->getProperty('optionsPageList');
        $optionsPageListProperty->setAccessible(true);
        $optionsPageListProperty->setValue($lyric, [
            \Lyric\Contracts\OptionsPages\PageBase::class
        ]);


        Actions\expectAdded('after_setup_theme')->once()->with(Mockery::type('callable'));


        $lyric->boot();

        // Asserts
        $this->assertTrue(has_action('after_setup_theme', 'function ()', 1));
    }
}
