<?php

namespace LyricTests\MetaBox;

use Mockery;
use Brain\Monkey;
use Lyric\Contracts\Fields\FieldFactory;
use Lyric\Contracts\MetaBox\MetaBoxBuilder;
use Brain\Monkey\Functions;
use Lyric\MetaBox\MetaBoxBase;
use PHPUnit\Framework\TestCase;

class MetaBoxBaseTest extends TestCase
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

    public function test_set_post_type_name_using_post_type_instance()
    {
        $fields = Mockery::mock(FieldFactory::class);

        $metaBoxBuilder = Mockery::mock(MetaBoxBuilder::class);

        $postType = Mockery::mock(\Lyric\Contracts\PostTypes\PostTypeBase::class);

        $metaBox = Mockery::mock(MetaBoxBase::class, [$metaBoxBuilder, $fields])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $metaBoxBuilder->shouldReceive('setPostType')
            ->once()
            ->with($postType)
            ->andReturnSelf();

        $this->assertInstanceOf(\Lyric\Contracts\MetaBox\MetaBoxBase::class, $metaBox->setPostType($postType));
    }

    public function test_should_register_extended_meta_box_class()
    {
        $fields = Mockery::mock(FieldFactory::class);

        $metaBoxBuilder = Mockery::mock(MetaBoxBuilder::class);

        $metaBox = Mockery::mock(MetaBoxBase::class, [$metaBoxBuilder, $fields])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();


        $metaBox->shouldReceive('settings')
            ->once()
            ->with($metaBoxBuilder)
            ->andReturn([

            ]);

        $metaBox->shouldReceive('fields')
            ->once()
            ->with(Mockery::type(FieldFactory::class))
            ->andReturn(['field-one']);


        $metaBoxBuilder->shouldReceive('fields')
            ->once()
            ->with(Mockery::type('array'))
            ->andReturnSelf();

        $metaBoxBuilder->shouldReceive('build')
            ->once()
            ->withNoArgs()
            ->andReturnSelf();

        $actionName = null;
        Functions\when('add_action')
            ->alias(function ($action, $callable) use (&$actionName) {
                $actionName = $action;
                $callable();
            });

        $metaBox->bind();
        $this->assertEquals('carbon_fields_register_fields', $actionName);
    }
}
