<?php

namespace LyricTests\OptionsPages;

use LyricTests\OptionsPages\Fixtures\OptionsPageFaker;
use LyricTests\LyricTestCase;
use Brain\Monkey\Functions;
use Mockery;

class PageBaseTest extends LyricTestCase
{
    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Bind options page
     */
    public function testBindOptionsPage()
    {
        // Arrange
        $builder = Mockery::mock(\Lyric\Contracts\OptionsPages\PageBuilder::class);
        $fieldFactory = Mockery::mock(\Lyric\Contracts\Fields\FieldFactory::class);

        $builder->shouldReceive('fields')
            ->once()
            ->with([])
            ->andReturnSelf();

        $builder->shouldReceive('build')
            ->once()
            ->withNoArgs()
            ->andReturnSelf();

        $actionName = null;
        Functions\when('add_action')
            ->alias(function ($action, $callable) use (&$actionName) {
                $actionName = $action;
                $callable();
            });

        // Act
        $optionsPageFaker = new OptionsPageFaker($builder, $fieldFactory);
        $optionsPageFaker->bind();

        // Asserts
        $this->assertEquals('carbon_fields_register_fields', $actionName);
    }

    /**
     * Bind child pages
     */
    public function testBindChildPages()
    {
        // Arrange
        $builder = Mockery::mock(\Lyric\Contracts\OptionsPages\PageBuilder::class);
        $fields = Mockery::mock(\Lyric\Contracts\Fields\FieldFactory::class);


        $builder->shouldReceive('parent')
            ->once()
            ->with(Mockery::type(\Lyric\OptionsPages\PageBase::class))
            ->andReturnSelf();

        // Act
        $optionsPageMain = new OptionsPageFaker($builder, $fields);
        $this->setProtectedProperty($optionsPageMain, 'childPages', [OptionsPageFaker::class]);
        $optionsPageMain->bind();

        // Asserts
        $this->assertTrue(has_action('carbon_fields_register_fields'));
    }
}
