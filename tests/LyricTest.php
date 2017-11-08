<?php

namespace LyricTests;

use Mockery;
use Brain\Monkey\Actions;
use Lyric\Lyric;
use League\Container\Container;
use Lyric\PostTypes\PostTypeBase;
use Lyric\OptionsPages\PageBase;

class LyricTest extends LyricTestCase
{
    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @return \Mockery\MockInterface
     */
    protected function getMockContainer()
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

    /**
     * Get lyric instance using static method
     */
    public function testGetLyricInstanceUsingStaticMethod()
    {
        $lyric = Lyric::make();

        $this->assertInstanceOf(Lyric::class, $lyric);
        $this->assertEquals($lyric, Lyric::make());
    }

    /**
     * Should register container
     */
    public function testShouldRegisterContainer()
    {
        $container = $this->getMockContainer();
        $lyric = new Lyric($container);

        $this->assertAttributeInstanceOf(Container::class, 'container', $lyric);
        $this->assertInstanceOf(Container::class, $lyric->container());
    }

    public function testRegisterClassThatTheLyricWillBindToWordPress()
    {
        //
        $container = $this->getMockContainer();

        $container->shouldReceive('has')
            ->once()
            ->with(\LyricTests\PostTypes\Fixtures\PostTypeFaker::class)
            ->andReturn(false);

        $container->shouldReceive('share')
            ->once()
            ->with(\LyricTests\PostTypes\Fixtures\PostTypeFaker::class)
            ->andReturnSelf();

        // Act
        $lyric = new Lyric($container);

        //Class using trait \Lyric\Hooks\BindToWordPress
        $lyric->addToBind(\LyricTests\PostTypes\Fixtures\PostTypeFaker::class);

        // Asserts
        $this->assertAttributeContains(
            \LyricTests\PostTypes\Fixtures\PostTypeFaker::class,
            'binds',
            $lyric
        );
    }

    /**
     * Should register post types
     */
    public function testShouldRegisterPostTypes()
    {
        $postType = Mockery::mock('PostTypeBase');
        $container = $this->getMockContainer();

        // Configure mocks
        $container->shouldReceive('share')
            ->once()
            ->with(PostTypeBase::class)
            ->andReturnSelf();

        $container->shouldReceive('withArgument')
            ->once()
            ->with($container)
            ->andReturnSelf();

        $container->shouldReceive('get')
            ->times(3)
            ->with(PostTypeBase::class)
            ->andReturn($postType);

        $container->shouldReceive('has')
            ->twice()
            ->with(PostTypeBase::class)
            ->andReturn(true);

        $container->shouldReceive('has')
            ->once()
            ->with('NonexistentPostType')
            ->andReturn(false);

        $postType->shouldReceive('getPostTypeName')
            ->once()
            ->withNoArgs()
            ->andReturn('lyric-post-type');


        $lyric = new Lyric($container);
        $lyric->addPostType(PostTypeBase::class);


        // Assertions
        $this->assertAttributeEquals(['lyric-post-type' => PostTypeBase::class], 'postTypeList', $lyric);
        $this->assertAttributeContains(PostTypeBase::class, 'binds', $lyric);
        $this->assertEquals($postType, $lyric->postType('lyric-post-type'));
        $this->assertEquals($postType, $lyric->postType(PostTypeBase::class));
        $this->assertFalse($lyric->postType('NonexistentPostType'));
    }

    /**
     * Save taxonomies to bind in lyric boot
     */
    public function testSaveTaxonomiesToBindInLyricBoot()
    {
        $taxonomyRegister = Mockery::mock(\Lyric\Contracts\Taxonomies\TaxonomyRegister::class);
        $container = $this->getMockContainer();
        $lyric = new Lyric($container);

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

        $container->shouldReceive('has')
            ->once()
            ->with(\Lyric\Taxonomies\TaxonomyBase::class)
            ->andReturn(true);

        $taxonomyRegister->shouldReceive('setPostType')
            ->once()
            ->with('lyric-post-type')
            ->andReturnSelf();

        // Execute
        $lyric->addTaxonomy(\Lyric\Taxonomies\TaxonomyBase::class, 'lyric-post-type');

        // Assertions
        $this->assertAttributeContains(\Lyric\Taxonomies\TaxonomyBase::class, 'binds', $lyric);
    }

    /**
     * Add meta box and save to bind in boot
     */
    public function testAddMetaBoxAndSaveToBindInBoot()
    {
        $metaBoxBuilder = Mockery::mock(\Lyric\Contracts\MetaBox\MetaBoxBuilder::class);
        $container = $this->getMockContainer();
        $lyric = new Lyric($container);

        // Configure mocks
        $container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\MetaBox\MetaBoxBuilder::class)
            ->andReturn($metaBoxBuilder);

        $container->shouldReceive('share')
            ->once()
            ->with(\Lyric\MetaBox\MetaBoxBase::class)
            ->andReturnSelf();

        $container->shouldReceive('withArgument')
            ->once()
            ->with($metaBoxBuilder)
            ->andReturnSelf();

        $container->shouldReceive('withArgument')
            ->once()
            ->with(\Lyric\Contracts\Fields\FieldFactory::class)
            ->andReturnSelf();

        $container->shouldReceive('has')
            ->once()
            ->with(\Lyric\MetaBox\MetaBoxBase::class)
            ->andReturn(true);

        $metaBoxBuilder->shouldReceive('setPostType')
            ->once()
            ->with('lyric-post-type')
            ->andReturnSelf();


        // Execute
        $lyric->addMetaBox(\Lyric\MetaBox\MetaBoxBase::class, 'lyric-post-type');

        // Assertions
        $this->assertAttributeContains(\Lyric\MetaBox\MetaBoxBase::class, 'binds', $lyric);
    }

    /**
     * Should register options page
     */
    public function testShouldRegisterOptionsPage()
    {
        $container = $this->getMockContainer();
        $lyric = new Lyric($container);


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

        $container->shouldReceive('has')
            ->once()
            ->with(PageBase::class)
            ->andReturn(true);

        // Act
        $lyric->addOptionsPage(PageBase::class);

        // Assertions
        $this->assertAttributeContains(PageBase::class, 'binds', $lyric);
    }

    /**
     * Bind post type to WordPress
     */
    public function testBindPostTypeToWordPress()
    {
        $container = $this->getMockContainer();

        // Used fake name to prevent mockery from call original final methods in class
        $postType = Mockery::mock("PostTypeBase");
        $taxonomy = Mockery::mock("TaxonomyBase");
        $metaBox = Mockery::mock("MetaBoxBase");
        $optionsPage = Mockery::mock("PageBase");

        // Configure mocks
        $container->shouldReceive('has')
            ->times(4)
            ->with(
                Mockery::anyOf('PostTypeBase', 'TaxonomyBase', 'MetaBoxBase', 'PageBase')
            )
            ->andReturn(true);

        $container->shouldReceive('get')
            ->times(4)
            ->with(
                Mockery::anyOf('PostTypeBase', 'TaxonomyBase', 'MetaBoxBase', 'PageBase')
            )
            ->andReturnUsing(function ($arg) use ($postType, $taxonomy, $metaBox, $optionsPage) {

                switch ($arg) {
                    case 'PostTypeBase':
                        return $postType;
                        break;
                    case 'TaxonomyBase':
                        return $taxonomy;
                        break;
                    case 'MetaBoxBase':
                        return $metaBox;
                        break;
                    case 'PageBase':
                        return $optionsPage;
                        break;
                }
            });

        $postType->shouldReceive('bind')
            ->once()
            ->withNoArgs();


        $taxonomy->shouldReceive('bind')
            ->once()
            ->withNoArgs();

        $metaBox->shouldReceive('bind')
            ->once()
            ->withNoArgs();

        $optionsPage->shouldReceive('bind')
            ->once()
            ->withNoArgs();


        Actions\expectAdded('after_setup_theme')->once()->with(Mockery::type('callable'));

        // configure Lyric
        $lyric = new Lyric($container);

        // Add mocks to Lyric
        $this->setProtectedProperty($lyric, 'postTypeList', [
            'lyric-post-type' => 'PostTypeBase',
        ]);

        $this->setProtectedProperty($lyric, 'binds', [
            'PostTypeBase',
            'TaxonomyBase',
            'MetaBoxBase',
            'PageBase',
        ]);

        // Act
        $lyric->boot();

        // Asserts
        $this->assertTrue(has_action('after_setup_theme', 'function ()', 1));
    }
}
