<?php

namespace LyricTests\Taxonomies;

use Lyric\Taxonomies\TaxonomyFactory;
use PHPUnit\Framework\TestCase;
use Mockery;
use Brain\Monkey;

class TaxonomyFactoryTest extends TestCase
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

    public function test_should_prepare_dependencies_to_base_taxonomy_classes()
    {
        $taxonomyRegister = Mockery::mock(\Lyric\Contracts\Taxonomies\TaxonomyRegister::class);
        $fieldsFactory = Mockery::mock(\Lyric\Contracts\Fields\FieldFactory::class);

        $taxonomyRegister->shouldReceive('setPostType')
            ->once()
            ->with('lyric-post-type')
            ->andReturnSelf();

        $taxonomyFactory = new TaxonomyFactory($taxonomyRegister, $fieldsFactory, 'lyric-post-type');

        $this->assertAttributeInstanceOf(
            \Lyric\Contracts\Taxonomies\TaxonomyRegister::class,
            'taxonomyRegister',
            $taxonomyFactory
        );

        $this->assertAttributeInstanceOf(
            \Lyric\Contracts\Fields\FieldFactory::class,
            'fieldFactory',
            $taxonomyFactory
        );
    }
}
