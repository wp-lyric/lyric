<?php

namespace Lyric\PostTypes;

use Lyric\Contracts\PostTypes\PostTypeRegister as PostTypeRegisterContract;
use Lyric\Support\Strings;

class PostTypeRegister implements PostTypeRegisterContract
{
    /**
     * The post type name.
     *
     * @var string
     */
    protected $postTypeName;

    /**
     * The human friendly singular name.
     *
     * @var string
     */
    protected $singular;

    /**
     * The human friendly plural name.
     *
     * @var string
     */
    protected $plural;

    /**
     * The post type slug.
     *
     * @var string
     */
    protected $slug;

    /**
     * The post type icon
     *
     * @var string
     */
    protected $icon = '';

    /**
     * The options passed for the post type.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The labels passed for the post type.
     *
     * @var array
     */
    protected $labels = [];

    /**
     * Register constructor
     *
     * @param  array|string $names
     */
    public function __construct($names)
    {
        $this->assignNames($names);
    }

    /**
     * Assign the post type names.
     * Accepts post type: name, slug, plural, singular.
     *
     * @param mixed $names Post type name as string or an array of names
     *
     * @return $this
     */
    public function assignNames($names)
    {
        $this->validateNames($names);

        if (is_string($names)) {
            $this->singular = $this->friendlyName($names);
            $this->plural = $this->singular . 's';
            $this->postTypeName = Strings::slug($this->plural);
            $this->slug($this->plural);

            return $this;
        }


        $this->postTypeName = Strings::slug($names['name']);

        if (isset($names['singular'])) {
            $this->singular = $names['singular'];
        } else {
            $this->singular = $this->friendlyName($this->postTypeName);
        }

        if (isset($names['plural'])) {
            $this->plural = $names['plural'];
        } else {
            $this->plural = $this->singular . 's';
        }

        if (isset($names['slug'])) {
            $this->slug($names['slug']);
        } else {
            $this->slug($this->plural);
        }

        return $this;
    }

    /**
     * Validate post type names options
     *
     * @param $names
     */
    private function validateNames($names)
    {
        if (!is_string($names) && !is_array($names)) {
            throw new \InvalidArgumentException('Use valid arguments(string|array) to define post type name');
        }

        if (is_array($names) && !array_key_exists('name', $names)) {
            throw new \InvalidArgumentException('Using array the [name] key is required to the post type');
        }
    }

    /**
     * Return friendly name
     *
     * @param $name
     *
     * @return string
     */
    protected function friendlyName($name)
    {
        return ucwords(strtolower(str_replace(['-', '_'], ' ', $name)));
    }

    /**
     * Return post type name
     *
     * @return string
     */
    public function getName()
    {
        return $this->postTypeName;
    }

    /**
     * Return list of default options
     *
     * @return array
     */
    protected function defaultOptions()
    {
        return [
            'public' => true,
            'menu_icon' => $this->icon,
            'rewrite' => [
                'slug' => $this->slug
            ]
        ];
    }

    /**
     * Set the post type options.
     *
     * @param array $options an array of post type options
     */
    public function options(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options list using configured labels and default values
     *
     * @return array
     */
    public function getOptions()
    {
        $options = $this->defaultOptions();
        $options['labels'] = $this->getLabels();

        return array_replace_recursive($options, $this->options);
    }

    /**
     * Return list of default labels used in register of the post type
     *
     * @return array
     */
    protected function defaultLabels()
    {
        return [
            'name' => sprintf(__('%s', 'lyric'), $this->plural),
            'singular_name' => sprintf(__('%s', 'lyric'), $this->singular),
            'menu_name' => sprintf(__('%s', 'lyric'), $this->plural),
            'name_admin_bar' => sprintf(__('%s', 'lyric'), $this->singular),
        ];
    }

    /**
     * Set the post type labels.
     *
     * @param array $labels An array of post type options
     */
    public function labels(array $labels)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * Get labels list using configured labels and default values
     *
     * @return array
     */
    public function getLabels()
    {
        return array_replace_recursive($this->defaultLabels(), $this->labels);
    }

    /**
     * Set post type slug
     *
     * @param string $slug
     *
     * @return $this
     */
    public function slug($slug, $format = true)
    {
        if ($format) {
            $this->slug = Strings::slug($slug);
        } else {
            $this->slug = $slug;
        }

        return $this;
    }

    /**
     * Set post type icon
     *
     * @param string $slug
     *
     * @return $this
     */
    public function icon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Bind methods to WordPress actions and filters.
     */
    public function bind()
    {
        add_action('init', function () {
            if (!post_type_exists($this->postTypeName)) {
                register_post_type($this->postTypeName, $this->getOptions());
            }
        }, 1);
    }
}