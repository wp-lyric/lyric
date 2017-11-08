<?php

namespace LyricTests\PostTypes;

use Lyric\Hooks\BindToWordPress;
use Lyric\PostTypes\PostTypeBase;
use LyricTests\LyricTestCase;
use LyricTests\PostTypes\Fixtures\MetaBoxFaker;
use LyricTests\PostTypes\Fixtures\PostTypeFaker;
use LyricTests\PostTypes\Fixtures\TaxonomyFaker;
use Mockery;
use Lyric\Contracts\PostTypes\PostTypeRegister;
use Lyric\Contracts\PostTypes\ColumnsFactory;

class PostTypeBaseTest extends LyricTestCase
{
    protected $taxonomyMockName;

    protected $metaBoxMockName;

    protected function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }


    protected function getContainer()
    {
        $container = Mockery::mock(\League\Container\ContainerInterface::class);
        $register = $this->getRegisterPostTypeMock();
        $columnsFactory = $this->getColumnFactoryMock();
        $metaBoxFactory = $this->getMetaBoxFactoryMock();
        $taxonomyFactory = $this->getTaxonomyFactoryMock();

        $container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\PostTypes\PostTypeRegister::class, Mockery::on(function ($argument) {
                if (is_array($argument) && count($argument) === 1) {
                    return is_string($argument[0]);
                }

                return false;
            }))
            ->andReturn($register);

        $container->shouldReceive('get')
            ->once()
            ->with(
                \Lyric\Contracts\MetaBox\MetaBoxFactory::class,
                Mockery::on(function ($argument) {
                    if (is_array($argument) && count($argument) === 3) {
                        if ($argument[0] !== \Lyric\Contracts\MetaBox\MetaBoxBuilder::class) {
                            return false;
                        }

                        if ($argument[1] !== \Lyric\Contracts\Fields\FieldFactory::class) {
                            return false;
                        }

                        return ($argument[2] instanceof PostTypeFaker);
                    }

                    return false;
                })
            )
            ->andReturn($metaBoxFactory);

        $container->shouldReceive('get')
            ->once()
            ->with(
                \Lyric\Contracts\Taxonomies\TaxonomyFactory::class,
                Mockery::on(function ($argument) {
                    if (is_array($argument) && count($argument) === 3) {
                        if ($argument[0] !== \Lyric\Contracts\Taxonomies\TaxonomyRegister::class) {
                            return false;
                        }

                        if ($argument[1] !== \Lyric\Contracts\Fields\FieldFactory::class) {
                            return false;
                        }

                        return ($argument[2] instanceof PostTypeFaker);
                    }

                    return false;
                })
            )
            ->andReturn($taxonomyFactory);

        $container->shouldReceive('get')
            ->once()
            ->with(\Lyric\Contracts\PostTypes\ColumnsFactory::class, Mockery::on(function ($argument) {
                if (is_array($argument) && count($argument) === 1) {
                    return ($argument[0] instanceof PostTypeFaker);
                }

                return false;
            }))
            ->andReturn($columnsFactory);

        return $container;
    }

    protected function getRegisterPostTypeMock()
    {
        $mock = Mockery::mock(PostTypeRegister::class);


        $mock->shouldReceive('bind')
            ->once()
            ->withNoArgs()
            ->andReturnSelf();

        return $mock;
    }

    protected function getColumnFactoryMock()
    {
        $mock = Mockery::mock(ColumnsFactory::class);

        $mock->shouldReceive('bind')
            ->once()
            ->withNoArgs()
            ->andReturnSelf();

        return $mock;
    }

    protected function getMetaBoxFactoryMock()
    {
        $mock = Mockery::mock(\Lyric\Contracts\MetaBox\MetaBoxFactory::class);

        $this->metaBoxMockName = get_class($mock);

        $mock->shouldReceive('addMetaBox')
            ->once()
            ->with(MetaBoxFaker::class)
            ->andReturnSelf();

        $mock->shouldReceive('bind')
            ->once()
            ->withNoArgs()
            ->andReturnSelf();

        return $mock;
    }

    protected function getTaxonomyFactoryMock()
    {
        $mock = Mockery::mock(\Lyric\Contracts\Taxonomies\TaxonomyFactory::class);

        $this->taxonomyMockName = get_class($mock);

        $mock->shouldReceive('addTaxonomy')
            ->once()
            ->with(TaxonomyFaker::class)
            ->andReturnSelf();

        $mock->shouldReceive('bind')
            ->once()
            ->withNoArgs()
            ->andReturnSelf();

        return $mock;
    }

    /**
     * Should build post type and dependencies
     */
    public function testShouldBuildPostTypeAndDependencies()
    {
        $postType = new PostTypeFaker($this->getContainer());
        $postType->bind();

        $this->assertAttributeEquals('post-type-faker', 'postTypeName', $postType);

        $resolvedList = $this->getObjectAttribute($postType, 'resolved');

        $this->assertInstanceOf(PostTypeRegister::class, $resolvedList[PostTypeRegister::class]);

        $this->assertInstanceOf(\Lyric\Contracts\MetaBox\MetaBoxFactory::class, $resolvedList[$this->metaBoxMockName]);

        $this->assertInstanceOf(
            \Lyric\Contracts\Taxonomies\TaxonomyFactory::class,
            $resolvedList[$this->taxonomyMockName]
        );

        $this->assertInstanceOf(ColumnsFactory::class, $resolvedList[ColumnsFactory::class]);
    }
}
