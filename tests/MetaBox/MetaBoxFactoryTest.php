<?php

namespace LyricTests\MetaBox;


use Lyric\MetaBox\MetaBoxFactory;
use LyricTests\LyricTestCase;
use LyricTests\MetaBox\Fixtures\MetaBoxBaseFake;
use Mockery;

class MetaBoxFactoryTest extends LyricTestCase
{

    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Should prepare dependencies to meta boxes base classes
     */
    public function testShouldPrepareDependenciesToMetaBoxesBaseClasses()
    {
        // Arrange
        $metaBoxBuilder = Mockery::mock(\Lyric\Contracts\MetaBox\MetaBoxBuilder::class);
        $fieldsFactory = Mockery::mock(\Lyric\Contracts\Fields\FieldFactory::class);

        $metaBoxBuilder->shouldReceive('setPostType')
            ->once()
            ->with('lyric-post-type')
            ->andReturnSelf();

        // Act
        $metaBoxFactory = new MetaBoxFactory($metaBoxBuilder, $fieldsFactory, 'lyric-post-type');
        $metaBoxFactory->addMetaBox(MetaBoxBaseFake::class);
        $metaBoxFactory->bind();

        // Assert
        $this->assertTrue(
            has_action('carbon_fields_register_fields', 'function ()') // Action called in MetaBoxBaseFake::class
        );
    }
}