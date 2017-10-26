<?php

namespace LyricTests\PostTypes;

use Lyric\PostTypes\Column;
use Lyric\PostTypes\ColumnsFactory;
use PHPUnit\Framework\TestCase;
use Mockery;

class ColumnsFactoryTest extends TestCase
{
    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_return_column_factory_instance()
    {
        $columnFactory = new ColumnsFactory('post-type');
        $column = $columnFactory->addColumn('Column Title', 'column-id');
        $columnExtra = $columnFactory->addColumn('Column Extra');

        $this->assertInstanceOf(Column::class, $column);

        $this->assertAttributeEquals('column-id', 'columnId', $column);
        $this->assertAttributeEquals('Column Title', 'title', $column);

        $this->assertAttributeEquals('column-extra', 'columnId', $columnExtra);
        $this->assertAttributeEquals('Column Extra', 'title', $columnExtra);

        $this->assertAttributeNotEmpty('columns', $columnFactory);
        $this->assertAttributeContainsOnly(Column::class, 'columns', $columnFactory);
    }

    public function test_should_build_all_registered_columns()
    {
        $column1 = Mockery::mock('Lyric\PostTypes\Column');
        $column2 = Mockery::mock('Lyric\PostTypes\Column');

        $column1->shouldReceive('bind')->once()->withNoArgs()->andReturnSelf();
        $column2->shouldReceive('bind')->once()->withNoArgs()->andReturnSelf();

        $columnFactory = new ColumnsFactory('post-type');

        $refection = new \ReflectionClass(ColumnsFactory::class);

        $columnsList = $refection->getProperty('columns');
        $columnsList->setAccessible(true);
        $columnsList->setValue($columnFactory, [$column1, $column2]);

        $this->assertNull(
            $columnFactory->bind()
        );
    }
}
