<?php

namespace LyricTests\PostTypes\Fixtures;

use Lyric\Contracts\Taxonomies\TaxonomyRegister;
use Lyric\Taxonomies\TaxonomyBase;

class TaxonomyFaker extends TaxonomyBase
{
    /**
     * @param TaxonomyRegister $taxonomyRegister
     *
     * @return mixed
     */
    protected function settings(TaxonomyRegister $taxonomyRegister)
    {
        return $taxonomyRegister;
    }
}
