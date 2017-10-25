<?php

namespace LyricTests\PostTypes;

use Mockery;
use PHPUnit\Framework\TestCase;
use Lyric\PostTypes\PostTypeBase;
use Lyric\Contracts\PostTypes\RegisterPostType;
use Lyric\Contracts\Fields\FieldBuilder;

class PostTypeBaseTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function test_should_init_register_and_configure_post_type_name()
    {
        $container = Mockery::mock(\League\Container\ContainerInterface::class);
        $register = Mockery::mock(RegisterPostType::class);

        // Configure Mocks
        $container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\PostTypes\RegisterPostType::class)
            ->andReturn($register);


        $register->shouldReceive('assignNames')
            ->once()
            ->with('lyric-post-type')
            ->andReturnSelf();

        $register->shouldReceive('getName')
            ->once()
            ->withNoArgs()
            ->andReturn('lyric-post-type');

        $customPostType = Mockery::namedMock('LyricPostType', PostTypeBase::class, [$container])->makePartial();

        // Set post type name
        $this->assertAttributeEquals('lyric-post-type', 'postTypeName', $customPostType);
        $this->assertEquals('lyric-post-type', $customPostType->postTypeName());

        //
        $this->assertAttributeEquals([RegisterPostType::class => $register], 'resolved', $customPostType);
        $this->assertAttributeEmpty('metaBoxes', $customPostType);
    }

    public function test_should_create_instance_metabox_and_save_in_simple_container()
    {
        $container = Mockery::mock(\League\Container\ContainerInterface::class);
        $register = Mockery::mock(RegisterPostType::class);

        $metaBoxBuilder = Mockery::mock(\Lyric\Contracts\MetaBox\MetaBoxBuilder::class);
        $fields = Mockery::mock(FieldBuilder::class);

        $metaBox = Mockery::mock(\Lyric\Contracts\Metabox\MetaBoxBase::class);


        // Configure mocks
        $container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\PostTypes\RegisterPostType::class)
            ->andReturn($register);

        $container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\MetaBox\MetaBoxBuilder::class)
            ->andReturn($metaBoxBuilder);

        $container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\Fields\FieldBuilder::class)
            ->andReturn($fields);


        $register->shouldReceive('assignNames')
            ->once()
            ->withAnyArgs()
            ->andReturnSelf();


        $metaBox->shouldReceive('setPostType')
            ->once()
            ->with(Mockery::type(PostTypeBase::class))
            ->andReturnSelf();

        $metaBoxClassName = get_class($metaBox);


        $customPostType = $this->getMockBuilder(PostTypeBase::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMetaBoxInstance'])
            ->getMockForAbstractClass();

        $customPostType->method('getMetaBoxInstance')
            ->with($this->equalTo($metaBoxClassName), $this->equalTo($metaBoxBuilder), $this->equalTo($fields))
            ->will($this->returnValue($metaBox));


        $reflectPostType = new \ReflectionClass(PostTypeBase::class);

        // Set Metaboxes list
        $metaBoxProperty = $reflectPostType->getProperty('metaBoxes');
        $metaBoxProperty->setAccessible(true);
        $metaBoxProperty->setValue($customPostType, [$metaBoxClassName]);

        // Set container
        $containerProperty = $reflectPostType->getProperty('container');
        $containerProperty->setAccessible(true);
        $containerProperty->setValue($customPostType, $container);


        // Invoke method to init metaBoxes
        $bootMethod = $reflectPostType->getMethod('boot');
        $bootMethod->setAccessible(true);
        $bootMethod->invoke($customPostType);


        // Assertions
        $this->assertAttributeNotEmpty('metaBoxes', $customPostType);
        $this->assertAttributeEquals([
            RegisterPostType::class => $register,
            $metaBoxClassName => $metaBox
        ],
            'resolved',
            $customPostType
        );
    }

    public function test_should_bind_post_type_to_wordpress()
    {
        $register = Mockery::mock(RegisterPostType::class);
        $metaBox = Mockery::mock(\Lyric\Contracts\Metabox\MetaBoxBase::class);

        // Configure mocks
        $register->shouldReceive('bind')
            ->once()
            ->withNoArgs()
            ->andReturnSelf();

        $metaBox->shouldReceive('bind')
            ->once()
            ->withNoArgs()
            ->andReturnSelf();


        // Post Type instance
        $customPostType = $this->getMockBuilder(PostTypeBase::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMetaBoxInstance'])
            ->getMockForAbstractClass();

        $reflectPostType = new \ReflectionClass(PostTypeBase::class);

        // Set resolved objects list
        $metaBoxProperty = $reflectPostType->getProperty('resolved');
        $metaBoxProperty->setAccessible(true);
        $metaBoxProperty->setValue($customPostType, [
            RegisterPostType::class => $register,
            \Lyric\Metabox\MetaBoxBase::class => $metaBox
        ]);

        $this->assertNull($customPostType->bind());
    }
}
