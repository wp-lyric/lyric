<?php

namespace Lyric\Taxonomies;

use Lyric\Support\Strings;

class TaxonomyRegister
{

    /**
     * Post type used to display taxonomy
     *
     * @var string
     */
    protected $postType;

    /**
     * Taxonomy name
     *
     * @var string
     */
    protected $taxonomyName;

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
     * The labels passed for the post type.
     *
     * @var array
     */
    protected $labels = [];

    /**
     * The options passed for the post type.
     *
     * @var array
     */
    protected $options = [];

    /**
     * TaxonomyRegister constructor.
     *
     * @param $names
     * @param $postType
     */
    public function __construct($names = null, $postType = null)
    {
        if(!is_null($names)) {
            $this->assignNames($names);
        }

        if(!is_null($postType)) {
            $this->addPostType($postType);
        }
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
            $this->taxonomyName = Strings::slug($this->plural);
            $this->slug($this->taxonomyName);

            return $this;
        }


        $this->taxonomyName = Strings::slug($names['name']);

        if (isset($names['singular'])) {
            $this->singular = $names['singular'];
        } else {
            $this->singular = $this->friendlyName($this->taxonomyName);
        }

        if (isset($names['plural'])) {
            $this->plural = $names['plural'];
        } else {
            $this->plural = $this->singular . 's';
        }

        if (isset($names['slug'])) {
            $this->slug($names['slug']);
        } else {
            $this->slug($this->taxonomyName);
        }

        return $this;
    }

    /**
     * Save post type used to display taxonomy
     *
     * @param $postType
     *
     * @return $this
     */
    public function addPostType($postType)
    {
        if ($postType instanceof \Lyric\Contracts\PostTypes\RegisterPostType) {
            $this->postType = $postType->getName();

            return $this;
        }

        $this->postType = $postType;

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
            throw new \InvalidArgumentException('Use valid arguments(string|array) to define taxonomy name');
        }

        if (is_array($names) && !array_key_exists('name', $names)) {
            throw new \InvalidArgumentException('The [name] key is required in ' . get_class($this) . '::assignNames');
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
     * Return list of default options
     *
     * @return array
     */
    protected function defaultOptions()
    {
        return [
            'public' => true,
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
     * Bind methods to WordPress actions and filters.
     */
    public function bind()
    {
        add_action('init', function () {
            if (!taxonomy_exists($this->taxonomyName)) {
                register_taxonomy($this->taxonomyName, $this->postType, $this->getOptions());
            }
        }, 1);
    }

}