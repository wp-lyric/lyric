<?php

namespace LyricTests\Taxonomies;

use Lyric\Taxonomies\TaxonomyBase;
use Lyric\Contracts\Fields\FieldFactory;
use Lyric\Contracts\Taxonomies\TaxonomyRegister;
use PHPUnit\Framework\TestCase;
use Mockery;
use Brain\Monkey;
use Brain\Monkey\Functions;

class TaxonomyBaseTest extends TestCase
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

    public function test_should_register_extended_taxonomy_class()
    {
        $fieldFactory = Mockery::mock(FieldFactory::class);

        $carbonContainer = Mockery::mock('alias:Carbon_Fields\Container\Container');

        $taxonomyRegister = Mockery::mock(TaxonomyRegister::class);

        // Configure mocks
        $taxonomyBase = Mockery::namedMock('LyricTaxonomy', TaxonomyBase::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $taxonomyBase->shouldReceive('settings')
            ->once()
            ->with($taxonomyRegister)
            ->andReturn($taxonomyRegister);

        $taxonomyRegister->shouldReceive('assignNames')
            ->once()
            ->with('lyric-taxonomy')
            ->andReturnSelf();

        $taxonomyRegister->shouldReceive('getName')
            ->twice()
            ->withNoArgs()
            ->andReturn('lyric-taxonomies');

        $taxonomyRegister->shouldReceive('bind')
            ->once()
            ->withNoArgs()
            ->andReturnSelf();

        $actionName = null;
        Functions\when('add_action')
            ->alias(function ($action, $callable) use (&$actionName) {
                $actionName = $action;
                $callable();
            });

        $carbonContainer->shouldReceive('factory')
            ->once()
            ->with('term_meta', 'lyric-taxonomies')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('where')
            ->once()
            ->with('term_taxonomy', '=', 'lyric-taxonomies')
            ->andReturnSelf();

        $carbonContainer->shouldReceive('add_fields')
            ->once()
            ->with(Mockery::type('array'))
            ->andReturnSelf();


        // Execute
        $taxonomyBase->boot($taxonomyRegister, $fieldFactory);
        $taxonomyBase->bind();

        // Assertions
        $this->assertEquals('carbon_fields_register_fields', $actionName);
    }
}
