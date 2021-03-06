<?php

namespace Lyric\PostTypes;

use Lyric\Contracts\PostTypes\PostTypeBase as PostTypeBaseContract;
use Lyric\Contracts\MetaBox\MetaBoxFactory;
use League\Container\ContainerInterface;
use Lyric\Contracts\PostTypes\PostTypeRegister;
use Lyric\Contracts\PostTypes\ColumnsFactory;
use Lyric\Contracts\Taxonomies\TaxonomyFactory;
use Lyric\Support\Strings;
use Lyric\Hooks\BindToWordPress;

abstract class PostTypeBase implements PostTypeBaseContract
{
    /**
     * Assign the post type names.
     * Accepts post type string or array
     * Options to array: name, slug, plural, singular.
     * @var string|array
     */
    protected $postTypeName;

    /**
     * List of the MetaBox\Base
     * @var array
     */
    protected $metaBoxes = [];

    /**
     * List of the TaxonomyBase instances
     * @var array
     */
    protected $taxonomies = [];

    /**
     * Container instance
     * @var ContainerInterface
     */
    protected $container;

    /**
     * List of the resolved instances
     * @var array
     */
    protected $resolved = [];

    /**
     * PostType constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->boot($container);
    }

    /**
     * Builder Post Type object
     *
     * @param ContainerInterface $container
     */
    final protected function boot(ContainerInterface $container)
    {
        $this->container = $container;

        // Resolve post type register
        $register = $this->registerPostTypeNames();
        $this->resolved[PostTypeRegister::class] = $this->postType($register);

        // Resolve meta-boxes
        if (!empty($this->metaBoxes)) {
            $this->resolveMetaBoxes();
        }

        // Resolve taxonomies
        if (!empty($this->taxonomies)) {
            $this->resolveTaxonomies();
        }

        // Resolve columns
        $columnFactory = $this->container->get(ColumnsFactory::class, [$this]);
        $this->resolved[ColumnsFactory::class] = $this->columns($columnFactory);
    }

    /**
     * Register the post type name
     * @return PostTypeRegister
     */
    final protected function registerPostTypeNames()
    {
        if (is_null($this->postTypeName)) {
            $className = (new \ReflectionClass($this))->getShortName();
            $this->postTypeName = Strings::slug($className);
        }

        $register = $this->container->get(\Lyric\Contracts\PostTypes\PostTypeRegister::class, [$this->postTypeName]);

        return $register;
    }

    /**
     * Register meta-boxes in MetaBoxFactory
     */
    final protected function resolveMetaBoxes()
    {
        $metaBoxFactory = $this->container->get(
            MetaBoxFactory::class,
            [
                \Lyric\Contracts\MetaBox\MetaBoxBuilder::class,
                \Lyric\Contracts\Fields\FieldFactory::class,
                $this,
            ]
        );


        foreach ($this->metaBoxes as $metaBox) {
            $metaBoxFactory->addMetaBox($metaBox);
        }

        $this->resolved[get_class($metaBoxFactory)] = $metaBoxFactory;
    }

    /**
     * Register taxonomies in TaxonomyFactory
     */
    final protected function resolveTaxonomies()
    {
        $taxonomyFactory = $this->container->get(
            TaxonomyFactory::class,
            [
                \Lyric\Contracts\Taxonomies\TaxonomyRegister::class,
                \Lyric\Contracts\Fields\FieldFactory::class,
                $this,
            ]
        );

        foreach ($this->taxonomies as $taxonomy) {
            $taxonomyFactory->addTaxonomy($taxonomy);
        }

        $this->resolved[get_class($taxonomyFactory)] = $taxonomyFactory;
    }

    /**
     * Return post type name
     * @return string
     */
    final public function getPostTypeName()
    {
        return $this->resolved[PostTypeRegister::class]->getName();
    }

    /**
     * Configure Post Type
     *
     * @param PostTypeRegister $register
     *
     * @return PostTypeRegister
     */
    protected function postType(PostTypeRegister $register)
    {
        return $register;
    }

    /**
     * Used to add and to configure columns in the actual post type
     *
     * @param ColumnsFactory $columnsFactory
     *
     * @return ColumnsFactory
     */
    public function columns(ColumnsFactory $columnsFactory)
    {
        return $columnsFactory;
    }

    /**
     * Bind Post Type to WordPress
     */
    public function bind()
    {
        foreach ($this->resolved as $object) {
            if ($object instanceof BindToWordPress) {
                $object->bind();
            }
        }
    }
}
