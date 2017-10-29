<?php

namespace LyricTests\PostTypes;

use Mockery;
use PHPUnit\Framework\TestCase;
use Lyric\PostTypes\PostTypeBase;
use Lyric\Contracts\PostTypes\RegisterPostType;
use Lyric\Contracts\Fields\FieldFactory;
use Lyric\Contracts\PostTypes\ColumnsFactory;

class PostTypeBaseTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function test_in_boot_is_necessary_configure_post_type_name_and_dependencies()
    {
        $container = Mockery::mock(\League\Container\ContainerInterface::class);
        $register = Mockery::mock(RegisterPostType::class);
        $columnsFactory = Mockery::mock(ColumnsFactory::class);

        // Configure Mocks
        $container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\PostTypes\RegisterPostType::class, ['lyric-post-type'])
            ->andReturn($register);

        $container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\PostTypes\ColumnsFactory::class, ['lyric-post-type'])
            ->andReturn($columnsFactory);

        $register->shouldReceive('getName')
            ->once()
            ->withNoArgs()
            ->andReturn('lyric-post-type');

        $lyricPostType = Mockery::namedMock('LyricPostType', PostTypeBase::class)->makePartial();

        $lyricPostType->boot($container);

        // Set post type name
        $this->assertAttributeEquals('lyric-post-type', 'postTypeName', $lyricPostType);

        //
        $this->assertAttributeEquals(
            [RegisterPostType::class => $register, ColumnsFactory::class => $columnsFactory],
            'resolved',
            $lyricPostType
        );
        $this->assertAttributeEmpty('metaBoxes', $lyricPostType);
    }

    public function test_should_create_instance_metabox_and_save_local_container()
    {
        $container = Mockery::mock(\League\Container\ContainerInterface::class);

        $metaBoxBuilder = Mockery::mock(\Lyric\Contracts\MetaBox\MetaBoxBuilder::class);
        $fieldFactory = Mockery::mock(FieldFactory::class);

        $metaBox = Mockery::mock(\Lyric\Contracts\Metabox\MetaBoxBase::class);

        $postTypeBase = Mockery::mock(PostTypeBase::class)->makePartial()->shouldAllowMockingProtectedMethods();


        // Configure mocks
        $container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\MetaBox\MetaBoxBuilder::class)
            ->andReturn($metaBoxBuilder);

        $container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\Fields\FieldFactory::class)
            ->andReturn($fieldFactory);

        $metaBox->shouldReceive('setPostType')
            ->once()
            ->with(Mockery::type(PostTypeBase::class))
            ->andReturnSelf();

        $metaBoxClassName = get_class($metaBox);

        $postTypeBase->shouldReceive('getMetaBoxInstance')
            ->once()
            ->with($metaBoxClassName, $metaBoxBuilder, $fieldFactory)
            ->andReturn($metaBox);

        /*
         * Use reflection to configure instance of the PostTypeBase and execute boot method
         */
        $reflectPostType = new \ReflectionClass(PostTypeBase::class);

        // Set container
        $containerProperty = $reflectPostType->getProperty('container');
        $containerProperty->setAccessible(true);
        $containerProperty->setValue($postTypeBase, $container);

        // Set MetaBoxes list
        $metaBoxProperty = $reflectPostType->getProperty('metaBoxes');
        $metaBoxProperty->setAccessible(true);
        $metaBoxProperty->setValue($postTypeBase, [$metaBoxClassName]);


        // Invoke method to init metaBoxes
        $postTypeBase->resolveMetaBoxes();


        // Assertions
        $this->assertAttributeNotEmpty('metaBoxes', $postTypeBase);
        $this->assertAttributeEquals([
            $metaBoxClassName => $metaBox,
        ],
            'resolved',
            $postTypeBase
        );
    }

    public function test_create_taxonomies_instances_and_save_to_bind()
    {
        $container = Mockery::mock(\League\Container\ContainerInterface::class);

        $taxonomyFactory = Mockery::mock(\Lyric\Contracts\Taxonomies\TaxonomyFactory::class);

        $postTypeBase = Mockery::mock(PostTypeBase::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $taxonomyClassName = get_class($taxonomyFactory);

        // Configure mocks
        $container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\Taxonomies\TaxonomyFactory::class, [
                \Lyric\Contracts\Taxonomies\TaxonomyRegister::class,
                \Lyric\Contracts\Fields\FieldFactory::class,
                'lyric-post-type'
            ])
            ->andReturn($taxonomyFactory);

        $taxonomyFactory->shouldReceive('addTaxonomy')
            ->once()
            ->with('TaxonomyBaseExtended')
            ->andReturnSelf();

        $postTypeBase->shouldReceive('postTypeName')
            ->once()
            ->withNoArgs()
            ->andReturn('lyric-post-type');

        /*
       * Use reflection to configure instance of the PostTypeBase and execute boot method
       */
        $reflectPostType = new \ReflectionClass(PostTypeBase::class);

        // Set container
        $containerProperty = $reflectPostType->getProperty('container');
        $containerProperty->setAccessible(true);
        $containerProperty->setValue($postTypeBase, $container);

        // Set MetaBoxes list
        $metaBoxProperty = $reflectPostType->getProperty('taxonomies');
        $metaBoxProperty->setAccessible(true);
        $metaBoxProperty->setValue($postTypeBase, ['TaxonomyBaseExtended']);

        // Execute
        $postTypeBase->resolveTaxonomies();

        // Assertions
        $this->assertAttributeNotEmpty('taxonomies', $postTypeBase);
        $this->assertAttributeEquals([
            $taxonomyClassName => $taxonomyFactory,
        ],
            'resolved',
            $postTypeBase
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
