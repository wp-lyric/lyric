<?php

namespace LyricTests\MetaBox;

use LyricTests\LyricTestCase;
use LyricTests\MetaBox\Fixtures\MetaBoxFaker;
use Mockery;
use Lyric\Contracts\Fields\FieldFactory;
use Lyric\Contracts\MetaBox\MetaBoxBuilder;
use Brain\Monkey\Functions;

class MetaBoxBaseTest extends LyricTestCase
{

    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Should register extended meta box class
     */
    public function testShouldRegisterExtendedMetaBoxClass()
    {
        $fieldsFactory = Mockery::mock(FieldFactory::class);

        $metaBoxBuilder = Mockery::mock(MetaBoxBuilder::class);

        $metaBox = new MetaBoxFaker($metaBoxBuilder, $fieldsFactory);


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
