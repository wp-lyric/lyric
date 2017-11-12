<?php

namespace LyricTests\Taxonomies;

use Lyric\Taxonomies\TaxonomyBase;
use Lyric\Taxonomies\TaxonomyFactory;
use LyricTests\LyricTestCase;
use LyricTests\PostTypes\Fixtures\TaxonomyFaker;
use Mockery;

class TaxonomyFactoryTest extends LyricTestCase
{
    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Should prepare dependencies to base taxonomy classes
     */
    public function testShouldPrepareDependenciesToBaseTaxonomyClasses()
    {
        $taxonomyRegister = Mockery::mock(\Lyric\Contracts\Taxonomies\TaxonomyRegister::class);
        $fieldsFactory = Mockery::mock(\Lyric\Contracts\Fields\FieldFactory::class);

        $taxonomyRegister->shouldReceive('setPostType')
            ->once()
            ->with('lyric-post-type')
            ->andReturnSelf();

        $taxonomyRegister->shouldReceive('assignNames')
            ->once()
            ->with(Mockery::type('string'))
            ->andReturnSelf();

        // Act
        $taxonomyFactory = new TaxonomyFactory($taxonomyRegister, $fieldsFactory, 'lyric-post-type');
        $taxonomyFactory->addTaxonomy(TaxonomyFaker::class);


        // Asserts
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

        $this->assertAttributeContainsOnly(
            TaxonomyBase::class,
            'taxonomies',
            $taxonomyFactory
        );
    }

    /**
     * Bind taxonomies to WordPress
     */
    public function testBindTaxonomiesToWordPress()
    {
        $taxonomyRegister = Mockery::mock(\Lyric\Contracts\Taxonomies\TaxonomyRegister::class);
        $fieldsFactory = Mockery::mock(\Lyric\Contracts\Fields\FieldFactory::class);
        $taxonomy = Mockery::mock(\Lyric\Taxonomies\TaxonomyBase::class);

        $taxonomyRegister->shouldReceive('setPostType')
            ->once()
            ->with('lyric-post-type')
            ->andReturnSelf();

        $taxonomy->shouldReceive('bind')
            ->once()
            ->withNoArgs()
            ->andReturnSelf();

        // Act
        $taxonomyFactory = new TaxonomyFactory($taxonomyRegister, $fieldsFactory, 'lyric-post-type');

        $this->setProtectedProperty($taxonomyFactory, 'taxonomies', [$taxonomy]);

        $this->assertNull($taxonomyFactory->bind());
    }
}
