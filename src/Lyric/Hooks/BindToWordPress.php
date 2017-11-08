<?php

namespace Lyric\Hooks;

interface BindToWordPress
{
    /**
     * Register all hooks here
     * This method is call to bind object to WordPress using actions and filters
     */
    public function bind();
}
