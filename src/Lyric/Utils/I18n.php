<?php

namespace Lyric\Utils;

trait I18n
{
    /**
     * The text domain is a unique identifier,
     * which makes sure WordPress can distinguish between all loaded translations
     *
     * @var string
     */
    protected $textDomain = 'lyric';

    /**
     * Set WordPress text domain
     *
     * @param $textdomain
     */
    public function setTextDomain($textDomain)
    {
        $this->textDomain = $textDomain;
    }

    /**
     * Get WordPress text domain
     *
     * @return string
     */
    public function textDomain()
    {
        return $this->textDomain;
    }
}