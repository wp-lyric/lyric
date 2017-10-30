<?php

namespace LyricTests\MetaBox;

use Lyric\MetaBox\MetaBoxFactory;
use PHPUnit\Framework\TestCase;
use Mockery;
use Brain\Monkey;

class MetaBoxFactoryTest extends TestCase
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

    public function test_should_prepare_dependencies_to_meta_boxes_base_classes()
    {
        $metaBoxBuilder = Mockery::mock(\Lyric\Contracts\MetaBox\MetaBoxBuilder::class);
        $fieldsFactory = Mockery::mock(\Lyric\Contracts\Fields\FieldFactory::class);

        $metaBoxBuilder->shouldReceive('setPostType')
            ->once()
            ->with('lyric-post-type')
            ->andReturnSelf();

        $metaBoxFactory = new MetaBoxFactory($metaBoxBuilder, $fieldsFactory, 'lyric-post-type');

        $this->assertAttributeInstanceOf(
            \Lyric\Contracts\MetaBox\MetaBoxBuilder::class,
            'metaBoxBuilder',
            $metaBoxFactory
        );

        $this->assertAttributeInstanceOf(
            \Lyric\Contracts\Fields\FieldFactory::class,
            'fieldFactory',
            $metaBoxFactory
        );
    }
}
