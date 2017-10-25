<?php

namespace LyricTests\OptionsPages;

use Lyric\Contracts\OptionsPages\PageBuilder as PageBuilderContract;
use Lyric\OptionsPages\PageBuilder;
use PHPUnit\Framework\TestCase;
use Mockery;

class PageBuilderTest extends TestCase
{

    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_set_menu_title()
    {
        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->title("Lyric Admin Page");

        $this->assertAttributeEquals('Lyric Admin Page', 'title', $adminPageBuilder);
        $this->assertAttributeEquals('lyric-admin-page', 'slug', $adminPageBuilder);
    }

    public function test_set_page_title()
    {
        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->pageTitle("Lyric Admin Page");

        $this->assertAttributeEquals('Lyric Admin Page', 'pageTitle', $adminPageBuilder);
    }

    public function test_set_page_slug()
    {
        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->slug("Lyric Slug");

        $this->assertAttributeEquals('lyric-slug', 'slug', $adminPageBuilder);
        $this->assertEquals('lyric-slug', $adminPageBuilder->getSlug());
    }


    public function test_set_menu_icon()
    {
        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->icon("lyric-icon");

        $this->assertAttributeEquals('lyric-icon', 'icon', $adminPageBuilder);
    }

    public function test_set_menu_position()
    {
        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->position("42.7");

        $this->assertAttributeEquals(42.7, 'position', $adminPageBuilder);
        $this->assertAttributeInternalType('float', 'position', $adminPageBuilder);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Use numeric position to register option page
     */
    public function test_throw_excerption_to_use_invalid_position()
    {
        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->position("five");
    }


    public function test_add_fields()
    {
        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->fields(['field-text']);

        $this->assertAttributeEquals(['field-text'], 'fields', $adminPageBuilder);
    }

    public function test_set_parent_page_using_string()
    {
        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->parent("lyric-options-page");

        $this->assertAttributeEquals('lyric-options-page', 'parent', $adminPageBuilder);
    }

    public function test_set_parent_page_using_object()
    {
        $parentPageBuilder = Mockery::mock(PageBuilderContract::class);

        $parentPageBuilder->shouldReceive('getSlug')
            ->once()
            ->withNoArgs()
            ->andReturn('lyric-options-page');


        $adminPageBuilder = new PageBuilder();
        $adminPageBuilder->parent($parentPageBuilder);


        $this->assertAttributeEquals('lyric-options-page', 'parent', $adminPageBuilder);
    }

    public function test_build_option_page_using_minimal_configuration()
    {
        $carbonContainer = Mockery::mock('alias:Carbon_Fields\Container\Container');

        $carbonContainer->shouldReceive('factory')
            ->once()
            ->with('theme_options', 'Lyric Options Page')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('set_page_file')
            ->once()
            ->with('lyric-options-page')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('add_fields')
            ->once()
            ->with([])
            ->andReturnSelf();

        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->title('Lyric Options Page');

        $this->assertInstanceOf(\Carbon_Fields\Container\Container::class, $adminPageBuilder->build());
    }

    public function test_build_option_page_using_full_options()
    {
        $carbonContainer = Mockery::mock('alias:Carbon_Fields\Container\Container');

        $carbonContainer->shouldReceive('factory')
            ->once()
            ->with('theme_options', 'Lyric Options')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('set_page_file')
            ->once()
            ->with('lyric-options')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('set_page_menu_title')
            ->once()
            ->with('Lyric Options Page')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('set_page_menu_position')
            ->once()
            ->with(42)
            ->andReturnSelf();

        $carbonContainer->shouldReceive('set_icon')
            ->once()
            ->with('lyric-icon')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('add_fields')
            ->once()
            ->with(['field-text'])
            ->andReturnSelf();

        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->title('Lyric Options')
            ->pageTitle('Lyric Options Page')
            ->icon('lyric-icon')
            ->position('42')
            ->fields(['field-text']);

        $return = $adminPageBuilder->build();

        $this->assertInstanceOf(\Carbon_Fields\Container\Container::class, $return);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Configure page title
     */
    public function test_throw_excerption_if_page_does_not_have_title()
    {
        $carbonContainer = Mockery::mock('alias:Carbon_Fields\Container\Container');

        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->build();
    }


    public function test_build_child_option_page_using_minimal_configuration()
    {
        $carbonContainer = Mockery::mock('alias:Carbon_Fields\Container\Container');

        $carbonContainer->shouldReceive('factory')
            ->once()
            ->with('theme_options', 'Lyric Child Options Page')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('set_page_parent')
            ->once()
            ->with('lyric-options')
            ->andReturnSelf();


        $carbonContainer->shouldReceive('set_page_file')
            ->once()
            ->with('lyric-child-options-page')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('add_fields')
            ->once()
            ->with([])
            ->andReturnSelf();

        $adminChildPageBuilder = new PageBuilder();

        $adminChildPageBuilder->title('Lyric Child Options Page');
        $adminChildPageBuilder->parent('lyric-options');

        $this->assertInstanceOf(\Carbon_Fields\Container\Container::class, $adminChildPageBuilder->build());
    }
}
