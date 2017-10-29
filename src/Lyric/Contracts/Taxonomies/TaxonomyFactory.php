<?php

namespace Lyric\Contracts\Taxonomies;

use Lyric\Contracts\Fields\FieldFactory;

interface TaxonomyFactory
{
    /**
     * TaxonomyFactory constructor.
     *
     * @param TaxonomyRegister $taxonomyRegister
     * @param FieldFactory $fieldFactory
     * @param $postType
     */
    public function __construct(TaxonomyRegister $taxonomyRegister, FieldFactory $fieldFactory, $postType);

    /**
     * Add new TaxonomyBase class
     *
     * @param $taxonomyBase
     */
    public function addTaxonomy($taxonomyBase);

    /**
     * Bind all TaxonomyBase classes
     */
    public function bind();
}