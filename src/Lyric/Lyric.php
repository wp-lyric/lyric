<?php

namespace Lyric;

use League\Container\Container;
use Lyric\Hooks\BindToWordPress;

class Lyric
{
    /**
     * Lyric instance
     * @var null|Lyric
     */
    protected static $instance = null;

    /**
     * Container instance
     * @var Container
     */
    protected $container;

    /**
     * List of post type class names
     * @var array
     */
    protected $postTypeList = [];

    /**
     * List of the classes to bind to WordPress
     *
     * @var array
     */
    protected $binds = [];

    /**
     * Lyric constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->initContainer();
    }

    /**
     * Get Lyric instance
     * @return Lyric|null
     */
    public static function make()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static(new Container());
        }

        return static::$instance;
    }

    /**
     * Configure services in container
     */
    protected function initContainer()
    {
        // Register Post Type Register
        $this->container()
            ->add(\Lyric\Contracts\PostTypes\PostTypeRegister::class, \Lyric\PostTypes\PostTypeRegister::class);

        // Register MetaBoxBuilder and MetaBoxFactory
        $this->container()
            ->add(\Lyric\Contracts\MetaBox\MetaBoxBuilder::class, \Lyric\MetaBox\MetaBoxBuilder::class);

        $this->container()
            ->add(\Lyric\Contracts\MetaBox\MetaBoxFactory::class, \Lyric\MetaBox\MetaBoxFactory::class);

        // Register ColumnsFactory
        $this->container()
            ->add(\Lyric\Contracts\PostTypes\ColumnsFactory::class, \Lyric\PostTypes\ColumnsFactory::class);

        // Register TaxonomyFactory and TaxonomyRegister
        $this->container()
            ->add(\Lyric\Contracts\Taxonomies\TaxonomyRegister::class, \Lyric\Taxonomies\TaxonomyRegister::class);

        $this->container()
            ->add(\Lyric\Contracts\Taxonomies\TaxonomyFactory::class, \Lyric\Taxonomies\TaxonomyFactory::class);

        // Register OptionsPage\Builder
        $this->container()
            ->add(\Lyric\Contracts\OptionsPages\PageBuilder::class, \Lyric\OptionsPages\PageBuilder::class);

        // Register Field Builder
        $this->container()
            ->share(\Lyric\Contracts\Fields\FieldFactory::class, \Lyric\Fields\FieldFactory::class);
    }

    /**
     * Return container instance
     * @return Container
     */
    public function container()
    {
        return $this->container;
    }

    /**
     * Bind to WordPress
     *
     * @param $class
     */
    public function addToBind($class)
    {
        if (in_array(BindToWordPress::class, class_implements($class))) {
            $this->binds[] = $class;
        }

        if (!$this->container()->has($class)) {
            $this->container()->share($class);
        }
    }

    /**
     * Register Post Type
     *
     * @param $postTypeClass
     *
     * @return $this
     */
    public function addPostType($postTypeClass)
    {
        $this->container()->share($postTypeClass)->withArgument($this->container);

        $postTypeInstance = $this->container()->get($postTypeClass);

        $this->postTypeList[$postTypeInstance->getPostTypeName()] = $postTypeClass;

        $this->addToBind($postTypeClass);

        return $this;
    }

    /**
     * Get Post Type instance
     *
     * @param $postTypeName
     *
     * @return mixed|object
     */
    public function postType($postTypeName)
    {
        if (array_key_exists($postTypeName, $this->postTypeList)) {
            $postTypeName = $this->postTypeList[$postTypeName];

            return $this->container()->get($postTypeName);
        }

        if ($this->container()->has($postTypeName)) {
            return $this->container()->get($postTypeName);
        }

        return false;
    }

    /**
     * Add TaxonomyBase to bind
     *
     * @param $taxonomyBaseClass
     * @param $postType
     *
     * @return $this
     */
    public function addTaxonomy($taxonomyBaseClass, $postType)
    {
        $registerTaxonomy = $this->container()->get(\Lyric\Contracts\Taxonomies\TaxonomyRegister::class);

        $registerTaxonomy->setPostType($postType);

        $this->container()->share($taxonomyBaseClass)
            ->withArgument($registerTaxonomy)
            ->withArgument(\Lyric\Contracts\Fields\FieldFactory::class);

        $this->addToBind($taxonomyBaseClass);

        return $this;
    }

    /**
     * Add MetaBoxBase to bind
     *
     * @param $metaBoxClass
     * @param $postType
     *
     * @return $this
     */
    public function addMetaBox($metaBoxClass, $postType)
    {
        $metaBoxBuilder = $this->container()->get(\Lyric\Contracts\MetaBox\MetaBoxBuilder::class);

        $metaBoxBuilder->setPostType($postType);

        $this->container()->share($metaBoxClass)
            ->withArgument($metaBoxBuilder)
            ->withArgument(\Lyric\Contracts\Fields\FieldFactory::class);

        $this->addToBind($metaBoxClass);

        return $this;
    }

    /**
     * Register options page in Lyric
     *
     * @param $optionsPageClass
     *
     * @return $this
     */
    public function addOptionsPage($optionsPageClass)
    {
        $this->container()->share($optionsPageClass)
            ->withArgument(\Lyric\Contracts\OptionsPages\PageBuilder::class)
            ->withArgument(\Lyric\Contracts\Fields\FieldFactory::class);

        $this->addToBind($optionsPageClass);

        return $this;
    }

    /**
     * Init Lyric
     * - Register post types
     * - Register taxonomies
     * - Register options page
     * - Register meta boxes
     * - Init Carbon Fields
     */
    public function boot()
    {
        foreach ($this->binds as $bind) {
            if ($this->container()->has($bind)) {
                ($this->container()->get($bind))->bind();
            }
        }

        add_action('after_setup_theme', function () {
            \Carbon_Fields\Carbon_Fields::boot();
        });
    }
}
