<?php

namespace LyricTests\PostTypes\Fixtures;

use Lyric\PostTypes\PostTypeBase;

class PostTypeFaker extends PostTypeBase
{
    protected $metaBoxes = [MetaBoxFaker::class];

    protected $taxonomies = [TaxonomyFaker::class];
}
