<?php

namespace LyricTests\OptionsPages;

use Lyric\Contracts\Fields\FieldFactory;
use Lyric\Contracts\OptionsPages\PageBuilder;
use Lyric\OptionsPages\PageBase;
use PHPUnit\Framework\TestCase;
use Brain\Monkey\Functions;
use Brain\Monkey;
use Mockery;

class PageBaseTest extends TestCase
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

    public function test_bind_options_page()
    {
        $builder = Mockery::mock(PageBuilder::class);

        $fields = Mockery::mock(FieldFactory::class);

        $optionsPageMain = Mockery::mock(PageBase::class,
            [$builder, $fields])->shouldAllowMockingProtectedMethods()->makePartial();


        $optionsPageMain->shouldReceive('page')
            ->once()
            ->with(Mockery::type(PageBuilder::class))
            ->andReturn($builder);

        $optionsPageMain->shouldReceive('fields')
            ->once()
            ->with(Mockery::type(FieldFactory::class))
            ->andReturn([$fields]);


        $builder->shouldReceive('fields')
            ->once()
            ->with(Mockery::type('array'))
            ->andReturnSelf();

        $builder->shouldReceive('build')
            ->once()
            ->withNoArgs()
            ->andReturnSelf();

        $actionName = null;
        Functions\when('add_action')
            ->alias(function ($action, $callable) use (&$actionName) {
                $actionName = $action;
                $callable();
            });

        $optionsPageMain->bind();
        $this->assertEquals('carbon_fields_register_fields', $actionName);
    }

    public function test_bind_child_pages()
    {
        $builder = Mockery::mock(PageBuilder::class);

        $fields = Mockery::mock(FieldFactory::class);

        $optionsPageMain = Mockery::mock(PageBase::class, [$builder, $fields])->shouldAllowMockingProtectedMethods()->makePartial();

        $pageChild = Mockery::namedMock('PageChild', \Lyric\Contracts\OptionsPages\PageBase::class . '[bind]');


        $optionsPageMain->shouldReceive('page')
            ->once()
            ->with(Mockery::type(PageBuilder::class))
            ->andReturn($builder);

        $optionsPageMain->shouldReceive('getOptionPageBaseInstance')
            ->once()
            ->with(get_class($pageChild), Mockery::type(PageBuilder::class), Mockery::type(FieldFactory::class))
            ->andReturn($pageChild);


        $builder->shouldReceive('parent')
            ->once()
            ->with(Mockery::type(PageBuilder::class))
            ->andReturnSelf();


        $pageChild->shouldReceive('bind')
            ->once()
            ->withAnyArgs()
            ->andReturnNull();


        $reflectOptionPage = new \ReflectionClass(PageBase::class);
        $childPagesProperty = $reflectOptionPage->getProperty('childPages');
        $childPagesProperty->setAccessible(true);
        $childClassName[] = get_class($pageChild);
        $childPagesProperty->setValue($optionsPageMain, $childClassName);


        $optionsPageMain->bind();

        $this->assertTrue(has_action('carbon_fields_register_fields'));
    }
}
