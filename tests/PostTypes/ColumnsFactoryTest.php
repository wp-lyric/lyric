<?php

namespace LyricTests\PostTypes;

use Lyric\PostTypes\Column;
use Lyric\PostTypes\ColumnsFactory;
use LyricTests\LyricTestCase;
use Mockery;

class ColumnsFactoryTest extends LyricTestCase
{
    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Return column factory instance
     */
    public function testReturnColumnFactoryInstance()
    {
        $columnFactory = new ColumnsFactory('post-type');
        $column = $columnFactory->addColumn('Column Title', 'column-id');
        $columnExtra = $columnFactory->addColumn('Column Extra');

        $this->assertInstanceOf(Column::class, $column);

        $this->assertAttributeEquals('column-id', 'columnName', $column);
        $this->assertAttributeEquals('Column Title', 'title', $column);

        $this->assertAttributeEquals('column-extra', 'columnName', $columnExtra);
        $this->assertAttributeEquals('Column Extra', 'title', $columnExtra);

        $this->assertAttributeNotEmpty('columns', $columnFactory);
        $this->assertAttributeContainsOnly(Column::class, 'columns', $columnFactory);
    }

    /**
     * Remove column
     */
    public function testRemoveColumn()
    {
        $columnFactory = new ColumnsFactory('post-type');
        $column = $columnFactory->removeColumn('column-id');

        $this->assertAttributeEquals('column-id', 'columnName', $column);
        $this->assertAttributeEquals(true, 'removeColumn', $column);

        $this->assertAttributeNotEmpty('columns', $columnFactory);
        $this->assertAttributeContainsOnly(Column::class, 'columns', $columnFactory);
    }

    /**
     * Remove column
     */
    public function testShouldBuildAllRegisteredColumns()
    {
        $column1 = Mockery::mock('Lyric\PostTypes\Column');
        $column2 = Mockery::mock('Lyric\PostTypes\Column');

        $column1->shouldReceive('bind')->once()->withNoArgs()->andReturnSelf();
        $column2->shouldReceive('bind')->once()->withNoArgs()->andReturnSelf();

        $columnFactory = new ColumnsFactory('post-type');

        $this->setProtectedProperty($columnFactory, 'columns', [$column1, $column2]);

        $this->assertNull(
            $columnFactory->bind()
        );
    }
}
