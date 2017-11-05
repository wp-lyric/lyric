<?php

namespace LyricTests\OptionsPages;

use Lyric\Contracts\OptionsPages\PageBuilder as PageBuilderContract;
use Lyric\OptionsPages\PageBuilder;
use LyricTests\LyricTestCase;
use LyricTests\OptionsPages\Fixtures\OptionsPageFaker;
use Mockery;

class PageBuilderTest extends LyricTestCase
{

    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     *Set menu title
     */
    public function testSetMenuTitle()
    {
        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->title("Lyric Admin Page");

        $this->assertAttributeEquals('Lyric Admin Page', 'title', $adminPageBuilder);
        $this->assertAttributeEquals('lyric-admin-page', 'slug', $adminPageBuilder);
    }

    /**
     * Set page title
     */
    public function testSetPageTitle()
    {
        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->pageTitle("Lyric Admin Page");

        $this->assertAttributeEquals('Lyric Admin Page', 'pageTitle', $adminPageBuilder);
    }

    /**
     * Set page slug
     */
    public function testSetPageSlug()
    {
        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->slug("Lyric Slug");

        $this->assertAttributeEquals('lyric-slug', 'slug', $adminPageBuilder);
        $this->assertEquals('lyric-slug', $adminPageBuilder->getSlug());
    }

    /**
     * Set menu icon
     */
    public function testSetMenuIcon()
    {
        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->icon("lyric-icon");

        $this->assertAttributeEquals('lyric-icon', 'icon', $adminPageBuilder);
    }

    /**
     * Set menu position
     */
    public function testSetMenuPosition()
    {
        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->position("42.7");

        $this->assertAttributeEquals(42.7, 'position', $adminPageBuilder);
        $this->assertAttributeInternalType('float', 'position', $adminPageBuilder);
    }

    /**
     * Throw exception to use invalid position
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Use numeric position to register option page
     */
    public function testThrowExceptionToUseInvalidPosition()
    {
        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->position("five");
    }

    /**
     * Add fields
     */
    public function testAddFields()
    {
        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->fields(['field-text']);

        $this->assertAttributeEquals(['field-text'], 'fields', $adminPageBuilder);
    }

    /**
     * Set parent page using string
     */
    public function testSetParentPageUsingString()
    {
        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->parent("lyric-options-page");

        $this->assertAttributeEquals('lyric-options-page', 'parent', $adminPageBuilder);
    }

    /**
     * Set parent page using object
     */
    public function testSetParentPageUsingObject()
    {
        $parentPageBuilder = Mockery::mock(PageBuilderContract::class);
        $factoryFields = Mockery::mock(\Lyric\Contracts\Fields\FieldFactory::class);
        $parentPageBase = new OptionsPageFaker($parentPageBuilder, $factoryFields);

        $parentPageBuilder->shouldReceive('getSlug')
            ->twice()
            ->withNoArgs()
            ->andReturn('lyric-options-page');


        $adminPageBuilder = new PageBuilder();
        $adminPageBuilder->parent($parentPageBuilder);

        $otherPageBuilder = new PageBuilder();
        $otherPageBuilder->parent($parentPageBase);


        $this->assertAttributeEquals('lyric-options-page', 'parent', $adminPageBuilder);
        $this->assertAttributeEquals('lyric-options-page', 'parent', $otherPageBuilder);
    }

    /**
     * Throw exception if page does not have title
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Configure page title
     */
    public function testThrowExceptionIfPageDoesNotHaveTitle()
    {
        $adminPageBuilder = new PageBuilder();

        $adminPageBuilder->build();
    }

    /**
     * Build option page using minimal configuration
     */
    public function testBuildOptionPageUsingMinimalConfiguration()
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

    /**
     * Build option page using full options
     */
    public function testBuildOptionPageUsingFullOptions()
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

        $carbonContainer->shouldReceive('set_page_parent')
            ->once()
            ->with('lyric-options-parent')
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
            ->parent('lyric-options-parent')
            ->icon('lyric-icon')
            ->position('42')
            ->fields(['field-text']);

        $return = $adminPageBuilder->build();

        $this->assertInstanceOf(\Carbon_Fields\Container\Container::class, $return);
    }

    public function testAddTabsInOptionsPage()
    {
        // Arrange
        $carbonContainer = Mockery::mock('alias:Carbon_Fields\Container\Container');

        $carbonContainer->shouldReceive('factory')
            ->once()
            ->with('theme_options', 'Lyric Options Page')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('set_page_file')
            ->once()
            ->with('lyric-options-page')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('add_tab')
            ->once()
            ->with('Custom Tab 1', ['input', 'textearea'])
            ->andReturnSelf();

        $carbonContainer->shouldReceive('add_tab')
            ->once()
            ->with('Custom Tab 2', ['gallery'])
            ->andReturnSelf();

        $carbonContainer->shouldReceive('add_tab')
            ->once()
            ->with('Custom Tab 3', ['select', 'radio'])
            ->andReturnSelf();

        // Act
        $adminPageBuilder = new PageBuilder();
        $adminPageBuilder->title('Lyric Options Page')
            ->fields([
                'Custom Tab 1' => ['input', 'textearea'],
                'Custom Tab 2' => ['gallery'],
                'Custom Tab 3' => ['select', 'radio']
            ]);

        // Assert
        $this->assertInstanceOf(\Carbon_Fields\Container\Container::class, $adminPageBuilder->build());
    }

    public function testCreateTabsToOptionsPageAutomatically()
    {
        // Arrange
        $carbonContainer = Mockery::mock('alias:Carbon_Fields\Container\Container');

        $carbonContainer->shouldReceive('factory')
            ->once()
            ->with('theme_options', 'Lyric Options Page')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('set_page_file')
            ->once()
            ->with('lyric-options-page')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('add_tab')
            ->once()
            ->with('Tab 1', ['input', 'textearea'])
            ->andReturnSelf();

        $carbonContainer->shouldReceive('add_tab')
            ->once()
            ->with('Tab 2', ['gallery'])
            ->andReturnSelf();

        $carbonContainer->shouldReceive('add_tab')
            ->once()
            ->with('Tab 3', ['select', 'radio'])
            ->andReturnSelf();

        // Act
        $adminPageBuilder = new PageBuilder();
        $adminPageBuilder->title('Lyric Options Page')
            ->withTabs()
            ->fields([
                ['input', 'textearea'],
                ['gallery'],
                ['select', 'radio']
            ]);

        // Assert
        $this->assertInstanceOf(\Carbon_Fields\Container\Container::class, $adminPageBuilder->build());
    }
}
