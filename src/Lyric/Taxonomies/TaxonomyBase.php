<?php

namespace Lyric\Taxonomies;

use Lyric\Contracts\Taxonomies\TaxonomyRegister;
use Lyric\Contracts\Fields\FieldFactory;
use Lyric\Hooks\BindToWordPress;
use Lyric\Support\Strings;
use Carbon_Fields\Container\Container;

abstract class TaxonomyBase implements BindToWordPress
{
    /**
     * Taxonomy Name
     *
     * @var string|null
     */
    protected $taxonomyName;

    /**
     * @var TaxonomyRegister
     */
    protected $taxonomyRegister;

    /**
     * @var FieldFactory
     */
    protected $fieldFactory;

    /**
     * TaxonomyBase constructor.
     *
     * @param TaxonomyRegister $taxonomyRegister
     * @param FieldFactory $fieldFactory
     */
    public function __construct(TaxonomyRegister $taxonomyRegister, FieldFactory $fieldFactory)
    {
        $this->boot($taxonomyRegister, $fieldFactory);
    }

    /**
     * Boot class
     *
     * @param TaxonomyRegister $taxonomyRegister
     * @param FieldFactory $fieldFactory
     */
    final public function boot(TaxonomyRegister $taxonomyRegister, FieldFactory $fieldFactory)
    {
        $taxonomyRegister = $this->registerTaxonomyNames($taxonomyRegister);
        $this->taxonomyRegister = $this->settings($taxonomyRegister);
        $this->fieldFactory = $fieldFactory;
    }

    /**
     * Register the post type name
     *
     * @param TaxonomyRegister $taxonomyRegister
     *
     * @return TaxonomyRegister
     */
    final protected function registerTaxonomyNames(TaxonomyRegister $taxonomyRegister)
    {
        if (is_null($this->taxonomyName)) {
            $className = (new \ReflectionClass($this))->getShortName();
            $this->taxonomyName = Strings::slug($className);
        }

        $taxonomyRegister->assignNames($this->taxonomyName);

        return $taxonomyRegister;
    }

    /**
     * Used to configure taxonomy
     *
     * @param TaxonomyRegister $taxonomyRegister
     *
     * @return TaxonomyRegister
     */
    abstract protected function settings(TaxonomyRegister $taxonomyRegister);

    /**
     * Used to build extra fields to taxonomy
     *
     * @param FieldFactory $fieldFactory
     *
     * @return array
     */
    protected function fields(FieldFactory $fieldFactory)
    {
        return [
            // Configure your fields using FieldFactory
        ];
    }

    /**
     * Bind taxonomy and fields to WordPress
     */
    public function bind()
    {
        $this->taxonomyRegister->bind();

        add_action('carbon_fields_register_fields', function () {
            $this->getCarbonFieldContainer()
                ->where('term_taxonomy', '=', $this->taxonomyRegister->getName())
                ->add_fields(
                    $this->fields($this->fieldFactory)
                );
        });
    }

    /**
     * Get Carbon Field Container
     *
     * @return \Carbon_Fields\Container\Term_Meta_Container|object
     */
    protected function getCarbonFieldContainer()
    {
        return Container::factory('term_meta', $this->taxonomyRegister->getName());
    }
}
