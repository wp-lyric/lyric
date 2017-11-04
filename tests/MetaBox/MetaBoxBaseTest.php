<?php

namespace LyricTests\MetaBox;

use LyricTests\LyricTestCase;
use Mockery;
use Lyric\Contracts\Fields\FieldFactory;
use Lyric\Contracts\MetaBox\MetaBoxBuilder;
use Brain\Monkey\Functions;
use Lyric\MetaBox\MetaBoxBase;

class MetaBoxBaseTest extends LyricTestCase
{

    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Set post type name using post type instance
     */
    public function testSetPostTypeNameUsingPostTypeInstance()
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

    /**
     * Should register extended meta box class
     */
    public function testShouldRegisterExtendedMetaBoxClass()
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
