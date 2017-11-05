<?php

namespace LyricTests\Metabox;

use Lyric\MetaBox\MetaBoxBuilder;
use LyricTests\LyricTestCase;
use Mockery;

class MetaBoxBuilderTest extends LyricTestCase
{

    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Set meta box title
     */
    public function testSetMetaBoxTitle()
    {
        $metaBoxBuilder = new MetaBoxBuilder();

        $metaBoxBuilder->title('MetBox Title');

        $this->assertAttributeEquals('MetBox Title', 'title', $metaBoxBuilder);
    }

    /**
     * Set meta box priority
     */
    public function testSetMetaBoxPriority()
    {
        $metaBoxBuilder = new MetaBoxBuilder();

        $metaBoxBuilder->priority('high');

        $this->assertAttributeEquals('high', 'priority', $metaBoxBuilder);
    }

    /**
     * Set meta box context
     */
    public function testSetMetaBoxContext()
    {
        $metaBoxBuilder = new MetaBoxBuilder();

        $metaBoxBuilder->context('advanced');

        $this->assertAttributeEquals('advanced', 'context', $metaBoxBuilder);
    }

    /**
     * Set meta box fields
     */
    public function testSetMetaBoxFields()
    {
        $metaBoxBuilder = new MetaBoxBuilder();

        $metaBoxBuilder->fields(['fields-list']);

        $this->assertAttributeEquals(['fields-list'], 'fields', $metaBoxBuilder);
    }

    /**
     * Throw excerption if page does not have title
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Configure Meta Box title
     */
    public function testThrowExcerptionIfPageDoesNotHaveTitle()
    {
        $metaBoxBuilder = new MetaBoxBuilder();

        $metaBoxBuilder->build();
    }

    /**
     * Build meta box using minimal configuration
     */
    public function testBuildMetaBoxUsingMinimalConfiguration()
    {
        $carbonContainer = Mockery::mock('alias:Carbon_Fields\Container\Container');

        $carbonContainer->shouldReceive('factory')
            ->once()
            ->with('post_meta', 'Lyric MetaBox')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('add_fields')
            ->once()
            ->with([])
            ->andReturnSelf();

        $metaBoxBuilder = new MetaBoxBuilder();

        $metaBoxBuilder->title('Lyric MetaBox');

        $this->assertInstanceOf(\Carbon_Fields\Container\Container::class, $metaBoxBuilder->build());
    }

    /**
     * Build meta box using full options
     */
    public function testBuildMetaBoxUsingFullOptions()
    {
        $carbonContainer = Mockery::mock('alias:Carbon_Fields\Container\Container');

        $carbonContainer->shouldReceive('factory')
            ->once()
            ->with('post_meta', 'Lyric MetaBox')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('show_on_post_type')
            ->once()
            ->with('lyric-post-type')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('set_priority')
            ->once()
            ->with('default')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('set_context')
            ->once()
            ->with('normal')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('add_fields')
            ->once()
            ->with(['field-text'])
            ->andReturnSelf();

        $metaBoxBuilder = new MetaBoxBuilder();

        $metaBoxBuilder->title('Lyric MetaBox')
            ->priority('default')
            ->context('normal')
            ->fields(['field-text']);
        $metaBoxBuilder->setPostType('lyric-post-type');

        $return = $metaBoxBuilder->build();

        $this->assertInstanceOf(\Carbon_Fields\Container\Container::class, $return);
    }

    /**
     * Build meta box using tabs to fields
     */
    public function testBuildMetaBoxUsingTabsToFields()
    {
        $carbonContainer = Mockery::mock('alias:Carbon_Fields\Container\Container');

        $carbonContainer->shouldReceive('factory')
            ->once()
            ->with('post_meta', 'Lyric MetaBox')
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

        $metaBoxBuilder = new MetaBoxBuilder();

        $metaBoxBuilder->title('Lyric MetaBox');

        $metaBoxBuilder->fields([
            'Custom Tab 1' => ['input', 'textearea'],
            'Custom Tab 2' => ['gallery'],
            'Custom Tab 3' => ['select', 'radio']
        ]);

        $this->assertInstanceOf(\Carbon_Fields\Container\Container::class, $metaBoxBuilder->build());
    }

    /**
     * Create tabs to meta box automatically
     */
    public function testCreateTabsToMetaBoxAutomatically()
    {
        // Arrange
        $carbonContainer = Mockery::mock('alias:Carbon_Fields\Container\Container');

        $carbonContainer->shouldReceive('factory')
            ->once()
            ->with('post_meta', 'Lyric MetaBox')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('add_tab')
            ->once()
            ->with('Tab 1', ['input', 'textearea'])
            ->andReturnSelf();

        $carbonContainer->shouldReceive('add_tab')
            ->once()
            ->with('Tab 2', ['gallery'])
            ->andReturnSelf();

        // Act
        $metaBoxBuilder = new MetaBoxBuilder();

        $metaBoxBuilder->title('Lyric MetaBox');

        $metaBoxBuilder->withTabs();

        $metaBoxBuilder->fields([
            ['input', 'textearea'],
            ['gallery'],
        ]);

        // Assert
        $this->assertInstanceOf(\Carbon_Fields\Container\Container::class, $metaBoxBuilder->build());
    }
}
