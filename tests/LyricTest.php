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
    /**
     * @var \Mockery\MockInterface
     */
    protected $container;

    public function setUp()
    {
        $this->container = Mockery::mock(Container::class);

        $this->container->shouldReceive('add')
            ->once()
            ->with(\Lyric\Contracts\PostTypes\RegisterPostType::class, \Lyric\PostTypes\RegisterPostType::class)
            ->andReturnSelf();

        $this->container->shouldReceive('withArgument')
            ->once()
            ->with(Mockery::type(\League\Container\Argument\RawArgument::class))
            ->andReturnSelf();

        $this->container->shouldReceive('add')
            ->once()
            ->with(\Lyric\Contracts\MetaBox\MetaBoxBuilder::class, \Lyric\MetaBox\MetaBoxBuilder::class)
            ->andReturnSelf();


        $this->container->shouldReceive('add')
            ->once()
            ->with(\Lyric\Contracts\OptionsPages\PageBuilder::class, \Lyric\OptionsPages\PageBuilder::class)
            ->andReturnSelf();

        $this->container->shouldReceive('share')
            ->once()
            ->with(\Lyric\Contracts\Fields\FieldBuilder::class, \Lyric\Fields\FieldBuilder::class)
            ->andReturnSelf();

        Monkey\setUp();
    }

    public function tearDown()
    {
        Monkey\tearDown();
        Mockery::close();
    }

    public function test_should_register_container()
    {
        $lyric = new Lyric($this->container);

        $this->assertAttributeInstanceOf(Container::class, 'container', $lyric);
        $this->assertInstanceOf(Container::class, $lyric->container());
    }

    public function test_should_register_post_types()
    {
        $postType = Mockery::mock(PostTypeBase::class);

        // Configure mocks
        $this->container->shouldReceive('share')
            ->once()
            ->with(PostTypeBase::class)
            ->andReturnSelf();

        $this->container->shouldReceive('withArgument')
            ->once()
            ->with($this->container)
            ->andReturnSelf();

        $this->container->shouldReceive('has')
            ->once()
            ->with(PostTypeBase::class)
            ->andReturn($postType);

        $this->container->shouldReceive('get')
            ->times(3)
            ->with(PostTypeBase::class)
            ->andReturn($postType);

        $postType->shouldReceive('postTypeName')
            ->once()
            ->withNoArgs()
            ->andReturn('lyric-post-type');


        $this->container->shouldReceive('has')
            ->once()
            ->with('FakePostType')
            ->andReturn(false);

        $lyric = new Lyric($this->container);
        $lyric->addPostType(PostTypeBase::class);


        // Assertions
        $this->assertAttributeEquals(['lyric-post-type' => PostTypeBase::class], 'postTypeList', $lyric);
        $this->assertEquals($postType, $lyric->postType('lyric-post-type'));
        $this->assertEquals($postType, $lyric->postType(PostTypeBase::class));
        $this->assertFalse($lyric->postType('FakePostType'));
    }

    public function test_should_register_options_page()
    {
        $optionsPage = Mockery::mock(PageBase::class);

        // Configure mocks
        $this->container->shouldReceive('share')
            ->once()
            ->with(PageBase::class)
            ->andReturnSelf();

        $this->container->shouldReceive('withArgument')
            ->once()
            ->with(\Lyric\Contracts\OptionsPages\PageBuilder::class)
            ->andReturnSelf();

        $this->container->shouldReceive('withArgument')
            ->once()
            ->with(\Lyric\Contracts\Fields\FieldBuilder::class)
            ->andReturnSelf();

        $lyric = new Lyric($this->container);
        $lyric->addOptionsPage(PageBase::class);


        // Assertions
        $this->assertAttributeContains(PageBase::class, 'optionsPageList', $lyric);
    }

    public function test_bind_post_type_to_wordpress()
    {
        // Use contact to create full mock
        $postType = Mockery::mock(\Lyric\Contracts\PostTypes\PostTypeBase::class);
        $optionsPage = Mockery::mock(\Lyric\Contracts\OptionsPages\PageBase::class);

        // Configure mocks
        $this->container->shouldReceive('has')
            ->once()
            ->with(\Lyric\Contracts\PostTypes\PostTypeBase::class)
            ->andReturn(true);

        $this->container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\PostTypes\PostTypeBase::class)
            ->andReturn($postType);

        $postType->shouldReceive('bind')
            ->once()
            ->withNoArgs();

        $this->container->shouldReceive('has')
            ->once()
            ->with(\Lyric\Contracts\OptionsPages\PageBase::class)
            ->andReturn(true);

        $this->container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\OptionsPages\PageBase::class)
            ->andReturn($optionsPage);

        $optionsPage->shouldReceive('bind')
            ->once()
            ->withNoArgs();


        $actions = null;


        // configure Lyric
        $lyric = new Lyric($this->container);

        $reflectLyric = new \ReflectionClass(Lyric::class);

        $postTypeListProperty = $reflectLyric->getProperty('postTypeList');
        $postTypeListProperty->setAccessible(true);
        $postTypeListProperty->setValue($lyric, [
            'lyric-post-type' => \Lyric\Contracts\PostTypes\PostTypeBase::class
        ]);

        $postTypeListProperty = $reflectLyric->getProperty('optionsPageList');
        $postTypeListProperty->setAccessible(true);
        $postTypeListProperty->setValue($lyric, [
            \Lyric\Contracts\OptionsPages\PageBase::class
        ]);


        Actions\expectAdded('after_setup_theme')->once()->with(Mockery::type('callable'));


        $lyric->boot();

        // Asserts
        $this->assertTrue(has_action('after_setup_theme', 'function ()', 1));
    }
}
