<?php

if(!function_exists('lyric')) {
    /**
     * Return Lyric instance
     *
     * @return \Lyric\Lyric|null
     */
    function lyric() {
        return \Lyric\Lyric::make();
    }
}