<?php

namespace Lyric\Contracts\Fields;

interface FieldFactory
{
    /**
     * This field allows to select and reorder multiple post type posts, taxonomy terms, users or comments
     *
     * @param $name
     * @param $label
     *
     * @return \Carbon_Fields\Field\Association_Field
     */
    public function association($name, $label);

    /**
     * The checkbox field create a single tick-able option with a label next to it
     *
     * @param $name
     * @param $label
     *
     * @return \Carbon_Fields\Field\Checkbox_Field
     */
    public function checkbox($name, $label);

    /**
     * Color picker field
     *
     * @param $name
     * @param $label
     *
     * @return \Carbon_Fields\Field\Color_Field
     */
    public function color($name, $label);

    /**
     * Renders a date picker
     *
     * @param $name
     * @param $label
     *
     * @return \Carbon_Fields\Field\Date_Field
     */
    public function date($name, $label);

    /**
     * Renders a date picker
     *
     * @param $name
     * @param $label
     *
     * @return \Carbon_Fields\Field\Date_Time_Field
     */
    public function dateTime($name, $label);

    /**
     * Renders a file upload field with a preview thumbnail of the uploaded file
     *
     * @param $name
     * @param $label
     *
     * @return \Carbon_Fields\Field\File_Field
     */
    public function file($name, $label);

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
    public function footerScripts($name, $label);

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
    public function headerScripts($name, $label);

    /**
     * Render custom HTML markup
     *
     * @param $name
     * @param $label
     *
     * @return \Carbon_Fields\Field\Html_Field
     */
    public function html($name, $label);

    /**
     * Renders an image upload button with a preview thumbnail of the uploaded image
     *
     * @param string $name Field Name
     * @param string|null $label Field Label
     *
     * @return \Carbon_Fields\Field\Image_Field
     */
    public function image($name, $label = null);

    /**
     * The map field provides a Google-powered map with an address search field
     *
     * @param string $name Field Name
     * @param string|null $label Field Label
     *
     * @return \Carbon_Fields\Field\Map_Field
     */
    public function map($name, $label = null);

    /**
     * Creates a list of tick-able options (checkboxes)
     *
     * @param string $name Field Name
     * @param string|null $label Field Label
     *
     * @return \Carbon_Fields\Field\Set_Field
     */
    public function multiCheckbox($name, $label = null);

    /**
     * Creates a radio selector with pre-defined options
     *
     * @param string $name Field Name
     * @param string|null $label Field Label
     *
     * @return \Carbon_Fields\Field\Radio_Field
     */
    public function radio($name, $label = null);

    /**
     * Behaves exactly like a Radio field, but the passed option values should be fully qualified urls to image
     * thumbnails which will be displayed instead of traditional labels
     *
     * @param string $name Field Name
     * @param string|null $label Field Label
     *
     * @return \Carbon_Fields\Field\Radio_Image_Field
     */
    public function radioImage($name, $label = null);

    /**
     * Creates a select box with pre-defined options
     *
     * @param string $name Field Name
     * @param string|null $label Field Label
     *
     * @return \Carbon_Fields\Field\Select_Field
     */
    public function select($name, $label = null);

    /**
     * Creates visual separator between adjacent fields
     *
     * @param string $name Field Name
     * @param string|null $label Field Label
     *
     * @return \Carbon_Fields\Field\Separator_Field
     */
    public function separator($name, $label = null);

    /**
     * This field renders the built-in WordPress tinyMCE WYSIWYG editor
     *
     * @param string $name Field Name
     * @param string|null $label Field Label
     *
     * @return \Carbon_Fields\Field\Rich_Text_Field
     */
    public function textEditor($name, $label = null);

    /**
     * The text field renders a text input field
     *
     * @param string $name Field Name
     * @param string|null $label
     *
     * @return \Carbon_Fields\Field\Text_Field
     */
    public function text($name, $label = null);

    /**
     * Multiline text input with HTML allowed
     *
     * @param string $name Field Name
     * @param string|null $label
     *
     * @return \Carbon_Fields\Field\Textarea_Field
     */
    public function textarea($name, $label = null);


    /**
     * Renders a time picker field
     *
     * @param string $name Field Name
     * @param string|null $label
     *
     * @return \Carbon_Fields\Field\Textarea_Field
     */
    public function time($name, $label = null);
}