<?php

namespace Lyric\Fields;

use Lyric\Contracts\Fields\FieldFactory as FieldFactoryContract;
use Carbon_Fields\Field\Field as CarbonField;

class FieldFactory implements FieldFactoryContract
{
    /**
     * Get field
     *
     * @param $field
     * @param $name
     * @param $label
     *
     * @return mixed
     */
    protected function getFieldInstance($field, $name, $label)
    {
        return CarbonField::factory($field, $name, $label);
    }

    /**
     * This field allows to select and reorder multiple post type posts, taxonomy terms, users or comments
     *
     * @param $name
     * @param $label
     *
     * @return \Carbon_Fields\Field\Association_Field
     */
    public function association($name, $label)
    {
        return $this->getFieldInstance('association', $name, $label);
    }

    /**
     * The checkbox field create a single tick-able option with a label next to it
     *
     * @param $name
     * @param $label
     *
     * @return \Carbon_Fields\Field\Checkbox_Field
     */
    public function checkbox($name, $label)
    {
        return $this->getFieldInstance('checkbox', $name, $label);
    }

    /**
     * Color picker field
     *
     * @param $name
     * @param $label
     *
     * @return \Carbon_Fields\Field\Color_Field
     */
    public function color($name, $label)
    {
        return $this->getFieldInstance('color', $name, $label);
    }

    /**
     * Renders a date picker
     *
     * @param $name
     * @param $label
     *
     * @return \Carbon_Fields\Field\Date_Field
     */
    public function date($name, $label)
    {
        return $this->getFieldInstance('date', $name, $label);
    }

    /**
     * Renders a date picker
     *
     * @param $name
     * @param $label
     *
     * @return \Carbon_Fields\Field\Date_Time_Field
     */
    public function dateTime($name, $label)
    {
        return $this->getFieldInstance('date_time', $name, $label);
    }

    /**
     * Renders a file upload field with a preview thumbnail of the uploaded file
     *
     * @param $name
     * @param $label
     *
     * @return \Carbon_Fields\Field\File_Field
     */
    public function file($name, $label)
    {
        return $this->getFieldInstance('file', $name, $label);
    }

    /**
     * Displays a text area, the contents of which will be automatically printed before the closing </body> of each
     * page (during wp_footer())
     *
     * Useful for printing Google Analytics tracking code, or user-defined javascript
     *
     * @param $name
     * @param $label
     *
     * @return \Carbon_Fields\Field\Footer_Scripts_Field
     */
    public function footerScripts($name, $label)
    {
        return $this->getFieldInstance('footer_scripts', $name, $label);
    }

    /**
     * Displays a text area, the contents of which will be automatically printed in the <head> of each page
     *
     * Useful for printing user-defined javascript, as well as styles, meta tags, etc
     *
     * @param $name
     * @param $label
     *
     * @return \Carbon_Fields\Field\Footer_Scripts_Field
     */
    public function headerScripts($name, $label)
    {
        return $this->getFieldInstance('header_scripts', $name, $label);
    }

    /**
     * Render custom HTML markup
     *
     * @param $name
     * @param $label
     *
     * @return \Carbon_Fields\Field\Html_Field
     */
    public function html($name, $label)
    {
        return $this->getFieldInstance('html', $name, $label);
    }

    /**
     * Renders an image upload button with a preview thumbnail of the uploaded image
     *
     * @param string $name Field Name
     * @param string|null $label Field Label
     *
     * @return \Carbon_Fields\Field\Image_Field
     */
    public function image($name, $label = null)
    {
        return $this->getFieldInstance('image', $name, $label);
    }

    /**
     * The map field provides a Google-powered map with an address search field
     *
     * @param string $name Field Name
     * @param string|null $label Field Label
     *
     * @return \Carbon_Fields\Field\Map_Field
     */
    public function map($name, $label = null)
    {
        return $this->getFieldInstance('map', $name, $label);
    }

    /**
     * Creates a list of tick-able options (checkboxes)
     *
     * @param string $name Field Name
     * @param string|null $label Field Label
     *
     * @return \Carbon_Fields\Field\Set_Field
     */
    public function multiCheckbox($name, $label = null)
    {
        return $this->getFieldInstance('set', $name, $label);
    }

    /**
     * Creates a radio selector with pre-defined options
     *
     * @param string $name Field Name
     * @param string|null $label Field Label
     *
     * @return \Carbon_Fields\Field\Radio_Field
     */
    public function radio($name, $label = null)
    {
        return $this->getFieldInstance('radio', $name, $label);
    }

    /**
     * Behaves exactly like a Radio field, but the passed option values should be fully qualified urls to image
     * thumbnails which will be displayed instead of traditional labels
     *
     * @param string $name Field Name
     * @param string|null $label Field Label
     *
     * @return \Carbon_Fields\Field\Radio_Image_Field
     */
    public function radioImage($name, $label = null)
    {
        return $this->getFieldInstance('radio_image', $name, $label);
    }

    /**
     * Creates a select box with pre-defined options
     *
     * @param string $name Field Name
     * @param string|null $label Field Label
     *
     * @return \Carbon_Fields\Field\Select_Field
     */
    public function select($name, $label = null)
    {
        return $this->getFieldInstance('select', $name, $label);
    }

    /**
     * Creates visual separator between adjacent fields
     *
     * @param string $name Field Name
     * @param string|null $label Field Label
     *
     * @return \Carbon_Fields\Field\Separator_Field
     */
    public function separator($name, $label = null)
    {
        return $this->getFieldInstance('separator', $name, $label);
    }

    /**
     * This field renders the built-in WordPress tinyMCE WYSIWYG editor
     *
     * @param string $name Field Name
     * @param string|null $label Field Label
     *
     * @return \Carbon_Fields\Field\Rich_Text_Field
     */
    public function textEditor($name, $label = null)
    {
        return $this->getFieldInstance('rich_text', $name, $label);
    }

    /**
     * The text field renders a text input field
     *
     * @param string $name Field Name
     * @param string|null $label
     *
     * @return \Carbon_Fields\Field\Text_Field
     */
    public function text($name, $label = null)
    {
        return $this->getFieldInstance('text', $name, $label);
    }

    /**
     * Multiline text input with HTML allowed
     *
     * @param string $name Field Name
     * @param string|null $label
     *
     * @return \Carbon_Fields\Field\Textarea_Field
     */
    public function textarea($name, $label = null)
    {
        return $this->getFieldInstance('textarea', $name, $label);
    }


    /**
     * Renders a time picker field
     *
     * @param string $name Field Name
     * @param string|null $label
     *
     * @return \Carbon_Fields\Field\Textarea_Field
     */
    public function time($name, $label = null)
    {
        return $this->getFieldInstance('time', $name, $label);
    }
}