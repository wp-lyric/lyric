<?php

namespace Lyric;

use League\Container\Container;

class Lyric
{
    /**
     * Lyric instance
     *
     * @var null|Lyric
     */
    protected static $instance = null;

    /**
     * Container instance
     *
     * @var Container
     */
    protected $container;

    /**
     * List of post type class names
     *
     * @var array
     */
    protected $postTypeList = [];

    /**
     * List of the options pages
     *
     * @var array
     */
    protected $optionsPageList = [];

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
     *
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
            ->add(\Lyric\Contracts\PostTypes\RegisterPostType::class, \Lyric\PostTypes\RegisterPostType::class)
            ->withArgument(new \League\Container\Argument\RawArgument('lyric-post-type'));

        // Register OptionsPage\Builder
        $this->container()
            ->add(\Lyric\Contracts\OptionsPages\PageBuilder::class, \Lyric\OptionsPages\PageBuilder::class);

        // Register MetaBox Builder
        $this->container()
            ->add(\Lyric\Contracts\MetaBox\MetaBoxBuilder::class, \Lyric\MetaBox\MetaBoxBuilder::class);

        // Register Field Builder
        $this->container()
            ->share(\Lyric\Contracts\Fields\FieldFactory::class, \Lyric\Fields\FieldFactory::class);
    }

    /**
     * Return container instance
     *
     * @return Container
     */
    public function container()
    {
        return $this->container;
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

        $this->postTypeList[$postTypeInstance->postTypeName()] = $postTypeClass;

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

        $this->optionsPageList[] = $optionsPageClass;

        return $this;
    }

    /**
     * Bind post types to WordPress
     */
    protected function bindPostTypes()
    {
        foreach ($this->postTypeList as $key => $postType) {
            if ($this->container()->has($postType)) {
                ($this->container()->get($postType))->bind();
            }
        }
    }

    /**
     * Bind options pages to WordPress
     */
    protected function bindOptionsPage()
    {
        foreach ($this->optionsPageList as $optionsPage) {
            if ($this->container()->has($optionsPage)) {
                ($this->container()->get($optionsPage))->bind();
            }
        }
    }

    /**
     * Init Lyric
     * - Register post types
     * - Init Carbon Fields
     */
    public function boot()
    {
        $this->bindPostTypes();

        $this->bindOptionsPage();

        add_action('after_setup_theme', function () {
            \Carbon_Fields\Carbon_Fields::boot();
        });
    }
}