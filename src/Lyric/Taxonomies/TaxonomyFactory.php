<?php

namespace Lyric\Taxonomies;

use Lyric\Contracts\Taxonomies\TaxonomyFactory as TaxonomyFactoryContract;
use Lyric\Contracts\Taxonomies\TaxonomyRegister;
use Lyric\Contracts\Fields\FieldFactory;

class TaxonomyFactory implements TaxonomyFactoryContract
{
    /**
     * @var TaxonomyRegister
     */
    protected $taxonomyRegister;

    /**
     * @var FieldFactory
     */
    protected $fieldFactory;

    /**
     * List of the TaxonomyBase to factory
     *
     * @var array
     */
    protected $taxonomies = [];

    /**
     * TaxonomyFactory constructor.
     *
     * @param TaxonomyRegister $taxonomyRegister
     * @param FieldFactory $fieldFactory
     * @param $postType
     */
    public function __construct(TaxonomyRegister $taxonomyRegister, FieldFactory $fieldFactory, $postType)
    {
        $taxonomyRegister->setPostType($postType);
        $this->taxonomyRegister = $taxonomyRegister;
        $this->fieldFactory = $fieldFactory;
    }

    /**
     * Add new TaxonomyBase class
     *
     * @param $taxonomyBase
     */
    public function addTaxonomy($taxonomyBase)
    {
        $this->taxonomies[] = new $taxonomyBase(clone $this->taxonomyRegister, $this->fieldFactory);

        return $this;
    }

    /**
     * Bind all TaxonomyBase classes
     */
    public function bind()
    {
        foreach ($this->taxonomies as $taxonomy) {
            if($taxonomy instanceof TaxonomyBase) {
                $taxonomy->bind();
            }
        }
    }
}