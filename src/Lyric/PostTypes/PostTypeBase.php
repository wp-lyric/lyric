<?php

namespace Lyric\PostTypes;

use Lyric\Contracts\PostTypes\PostTypeBase as PostTypeBaseContract;
use League\Container\ContainerInterface;
use Lyric\Contracts\PostTypes\RegisterPostType;
use Lyric\Contracts\PostTypes\ColumnsFactory;
use Lyric\Contracts\Fields\FieldFactory;
use Lyric\Support\Strings;

abstract class PostTypeBase implements PostTypeBaseContract
{
    /**
     * Name used to identify Post Type
     *
     * @var string|array
     */
    protected $postTypeName;

    /**
     * List of the MetaBox\Base
     *
     * @var array
     */
    protected $metaBoxes = [];

    protected $taxonomies = [];

    /**
     * Container instance
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * List of the resolved instances
     *
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
     * @param RegisterPostType $register
     * @param FieldFactory $fields
     */
    final public function boot(ContainerInterface $container)
    {
        $this->container = $container;

        // Resolve post type register
        $register = $this->registerPostTypeNames();
        $this->resolved[RegisterPostType::class] = $this->postType($register);

        // Resolve meta-boxes
        if (!empty($this->metaBoxes)) {
            $this->resolveMetaBoxes();
        }

        // Resolve taxonomies
        if (!empty($this->taxonomies)) {
            $this->resolveTaxonomies();
        }

        // Resolve columns
        $columnFactory = $this->container->get(ColumnsFactory::class, [$this->postTypeName()]);
        $this->resolved[ColumnsFactory::class] = $this->columns($columnFactory);
    }

    /**
     * Register the post type name
     *
     * @param RegisterPostType $register
     *
     * @return RegisterPostType
     */
    final protected function registerPostTypeNames()
    {
        if (is_null($this->postTypeName)) {
            $className = (new \ReflectionClass($this))->getShortName();
            $this->postTypeName = Strings::slug($className);
        }

        $register = $this->container->get(\Lyric\Contracts\PostTypes\RegisterPostType::class, [$this->postTypeName]);

        return $register;
    }

    /**
     * Builder meta box instances and register objects
     */
    final public function resolveMetaBoxes()
    {
        foreach ($this->metaBoxes as $metaBoxBase) {
            $metaBoxInstance = $this->getMetaBoxInstance(
                $metaBoxBase,
                $this->container->get(\Lyric\Contracts\MetaBox\MetaBoxBuilder::class),
                $this->container->get(FieldFactory::class)
            );

            if ($metaBoxInstance instanceof \Lyric\Contracts\Metabox\MetaBoxBase) {
                $metaBoxInstance->setPostType($this);
                $this->resolved[$metaBoxBase] = $metaBoxInstance;
            }
        }
    }

    /**
     * Used to build meta-box instance
     *
     * @param $class
     * @param $fieldFactory
     *
     * @return null|object
     */
    protected function getMetaBoxInstance($class, $metaBoxBuilder, $fieldFactory)
    {
        return class_exists($class) ? new $class($metaBoxBuilder, $fieldFactory) : null;
    }

    final public function resolveTaxonomies()
    {
        $taxonomyFactory = $this->container->get(
            \Lyric\Contracts\Taxonomies\TaxonomyFactory::class,
            [
                \Lyric\Contracts\Taxonomies\TaxonomyRegister::class,
                \Lyric\Contracts\Fields\FieldFactory::class,
                $this->postTypeName()
            ]
        );

        foreach ($this->taxonomies as $taxonomy) {
            $taxonomyFactory->addTaxonomy($taxonomy);
        }

        $this->resolved[get_class($taxonomyFactory)] = $taxonomyFactory;
    }

    /**
     * Return post type name
     *
     * @return string
     */
    public function postTypeName()
    {
        return $this->resolved[RegisterPostType::class]->getName();
    }

    /**
     * Configure Post Type
     *
     * @param RegisterPostType $register
     *
     * @return RegisterPostType
     */
    protected function postType(RegisterPostType $register)
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
    final public function bind()
    {
        foreach ($this->resolved as $object) {
            if (is_object($object) && method_exists($object, 'bind')) {
                $object->bind();
            }
        }
    }
}