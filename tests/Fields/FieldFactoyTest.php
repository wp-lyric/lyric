<?php

namespace LyricTests\Fields;

use Mockery;
use Lyric\Fields\FieldFactory;
use PHPUnit\Framework\TestCase;

class FieldFactoyTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function fields_provider()
    {
        return [
            ['association'],
            ['checkbox'],
            ['color'],
            ['date'],
            ['file'],
            ['html'],
            ['image'],
            ['map'],
            ['radio'],
            ['select'],
            ['separator'],
            ['text'],
            ['textarea'],
            ['time'],
        ];
    }

    /**
     * @dataProvider fields_provider
     */
    public function test_build_fields($fieldName)
    {
        $carbonFields = Mockery::mock('alias:Carbon_Fields\Field\Field');

        $carbonFields->shouldReceive('factory')
            ->once()
            ->with($fieldName, 'field-id', 'Field Title')
            ->andReturnSelf();

        $fields = new FieldFactory();

        $this->assertInstanceOf(
            \Carbon_Fields\Field\Field::class,
            $fields->$fieldName('field-id', 'Field Title')
        );
    }

    public function test_build_date_time_field()
    {
        $carbonFields = Mockery::mock('alias:Carbon_Fields\Field\Field');

        $carbonFields->shouldReceive('factory')
            ->once()
            ->with('date_time', 'field-id', 'Field Title')
            ->andReturnSelf();

        $fields = new FieldFactory();

        $this->assertInstanceOf(
            \Carbon_Fields\Field\Field::class,
            $fields->dateTime('field-id', 'Field Title')
        );

    }

    public function test_build_footer_scripts_field()
    {
        $carbonFields = Mockery::mock('alias:Carbon_Fields\Field\Field');

        $carbonFields->shouldReceive('factory')
            ->once()
            ->with('footer_scripts', 'field-id', 'Field Title')
            ->andReturnSelf();

        $fields = new FieldFactory();

        $this->assertInstanceOf(
            \Carbon_Fields\Field\Field::class,
            $fields->footerScripts('field-id', 'Field Title')
        );

    }

    public function test_build_header_scripts_field()
    {
        $carbonFields = Mockery::mock('alias:Carbon_Fields\Field\Field');

        $carbonFields->shouldReceive('factory')
            ->once()
            ->with('header_scripts', 'field-id', 'Field Title')
            ->andReturnSelf();

        $fields = new FieldFactory();

        $this->assertInstanceOf(
            \Carbon_Fields\Field\Field::class,
            $fields->headerScripts('field-id', 'Field Title')
        );

    }

    public function test_build_multi_checkbox_field()
    {
        $carbonFields = Mockery::mock('alias:Carbon_Fields\Field\Field');

        $carbonFields->shouldReceive('factory')
            ->once()
            ->with('set', 'field-id', 'Field Title')
            ->andReturnSelf();

        $fields = new FieldFactory();

        $this->assertInstanceOf(
            \Carbon_Fields\Field\Field::class,
            $fields->multiCheckbox('field-id', 'Field Title')
        );

    }

    public function test_build_radio_image_field()
    {
        $carbonFields = Mockery::mock('alias:Carbon_Fields\Field\Field');

        $carbonFields->shouldReceive('factory')
            ->once()
            ->with('radio_image', 'field-id', 'Field Title')
            ->andReturnSelf();

        $fields = new FieldFactory();

        $this->assertInstanceOf(
            \Carbon_Fields\Field\Field::class,
            $fields->radioImage('field-id', 'Field Title')
        );

    }

    public function test_build_rich_text_field()
    {
        $carbonFields = Mockery::mock('alias:Carbon_Fields\Field\Field');

        $carbonFields->shouldReceive('factory')
            ->once()
            ->with('rich_text', 'field-id', 'Field Title')
            ->andReturnSelf();

        $fields = new FieldFactory();

        $this->assertInstanceOf(
            \Carbon_Fields\Field\Field::class,
            $fields->textEditor('field-id', 'Field Title')
        );
    }
}
