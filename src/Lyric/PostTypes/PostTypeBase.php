<?php

namespace Lyric\PostTypes;

use Lyric\Contracts\PostTypes\PostTypeBase as PostTypeBaseContract;
use League\Container\ContainerInterface;
use Lyric\Contracts\PostTypes\RegisterPostType;
use Lyric\Contracts\Fields\FieldBuilder;
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
        $this->container = $container;
        $this->boot();
    }

    /**
     * Builder Post Type object
     *
     * @param RegisterPostType $register
     * @param FieldBuilder $fields
     */
    final protected function boot()
    {
        $this->resolvePostTypeRegister();

        if (!empty($this->metaBoxes)) {
            $this->resolveMetaBoxes();
        }
    }

    /**
     * Resolve RegisterPostType instance
     */
    final  protected function resolvePostTypeRegister()
    {
        $register = $this->container->get(\Lyric\Contracts\PostTypes\RegisterPostType::class);
        $register = $this->registerPostTypeNames($register);
        $this->resolved[RegisterPostType::class] = $this->postType($register);
    }

    /**
     * Register the post type name
     *
     * @param RegisterPostType $register
     *
     * @return RegisterPostType
     */
    final protected function registerPostTypeNames(RegisterPostType $register)
    {
        if (is_null($this->postTypeName)) {
            $className = (new \ReflectionClass($this))->getShortName();
            $this->postTypeName = Strings::slug($className);
        }

        $register->assignNames($this->postTypeName);

        return $register;
    }

    /**
     * Builder meta box instances and register objects
     */
    final protected function resolveMetaBoxes()
    {
        foreach ($this->metaBoxes as $metaBoxBase) {
            $metaBoxInstance = $this->getMetaBoxInstance(
                $metaBoxBase,
                $this->container->get(\Lyric\Contracts\MetaBox\MetaBoxBuilder::class),
                $this->container->get(FieldBuilder::class)
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
     * @param $fieldBuilder
     *
     * @return null|object
     */
    protected function getMetaBoxInstance($class, $metaBoxBuilder, $fieldBuilder)
    {
        return class_exists($class) ? new $class($metaBoxBuilder, $fieldBuilder) : null;
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
     * Should Configure Post Type
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