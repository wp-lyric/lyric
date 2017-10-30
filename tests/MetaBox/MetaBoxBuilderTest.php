<?php

namespace LyricTests\Metabox;

use Lyric\MetaBox\MetaBoxBuilder;
use Lyric\Contracts\PostTypes\PostTypeBase;
use PHPUnit\Framework\TestCase;
use Mockery;

class MetaBoxBuilderTest extends TestCase
{

    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_set_meta_box_title()
    {
        $metaBoxBuilder = new MetaBoxBuilder();

        $metaBoxBuilder->title('MetBox Title');

        $this->assertAttributeEquals('MetBox Title', 'title', $metaBoxBuilder);
    }

    public function test_set_meta_box_priority()
    {
        $metaBoxBuilder = new MetaBoxBuilder();

        $metaBoxBuilder->priority('high');

        $this->assertAttributeEquals('high', 'priority', $metaBoxBuilder);
    }

    public function test_set_meta_box_context()
    {
        $metaBoxBuilder = new MetaBoxBuilder();

        $metaBoxBuilder->context('advanced');

        $this->assertAttributeEquals('advanced', 'context', $metaBoxBuilder);
    }

    public function test_set_meta_box_fields()
    {
        $metaBoxBuilder = new MetaBoxBuilder();

        $metaBoxBuilder->fields(['fields-list']);

        $this->assertAttributeEquals(['fields-list'], 'fields', $metaBoxBuilder);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Configure Meta Box title
     */
    public function test_throw_excerption_if_page_does_not_have_title()
    {
        $carbonContainer = Mockery::mock('alias:Carbon_Fields\Container\Container');

        $metaBoxBuilder = new MetaBoxBuilder();

        $metaBoxBuilder->build();
    }

    public function test_build_meta_box_using_minimal_configuration()
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

    public function test_build_meta_box_using_full_options()
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
}
