<?php

namespace Lyric\OptionsPages;

use Lyric\Contracts\Fields\FieldBuilder;
use Lyric\Contracts\OptionsPages\PageBuilder;
use Lyric\Contracts\OptionsPages\PageBase as PageBaseContract;

abstract class PageBase implements PageBaseContract
{
    /**
     * Array of the class to configure child options pages
     *
     * @var array
     */
    protected $childPages;

    /**
     * @var PageBuilder
     */
    protected $pageBuilder;

    /**
     * @var FieldBuilder
     */
    protected $fieldBuilder;

    /**
     * PageBase constructor.
     *
     * @param PageBuilder $pageBuilder
     * @param FieldBuilder $fieldBuilder
     */
    public function __construct(PageBuilder $pageBuilder, FieldBuilder $fieldBuilder)
    {
        $this->pageBuilder = $pageBuilder;
        $this->fieldBuilder = $fieldBuilder;
    }

    /**
     * Build Page
     *
     * @param PageBuilder $pageBuilder
     *
     * @return PageBuilder
     */
    abstract protected function page(PageBuilder $pageBuilder);

    /**
     * Return array of the Fields instances
     *
     * @param FieldBuilder $fieldBuilder
     *
     * @return array
     */
    abstract protected function fields(FieldBuilder $fieldBuilder);

    /**
     * Bind option page to WordPress
     */
    final public function bind()
    {
        $childPageBuilder = clone $this->pageBuilder;

        $this->page($this->pageBuilder);

        add_action('carbon_fields_register_fields', function () {
            $this->pageBuilder->fields(
                $this->fields($this->fieldBuilder)
            );

            $this->pageBuilder->build();
        });


        // Bind child pages
        if (is_array($this->childPages) || !empty($this->childPages)) {

            $childPageBuilder->parent($this->pageBuilder);

            foreach ($this->childPages as $childPage) {
                $childPageInstance = $this->getOptionPageBaseInstance(
                    $childPage,
                    clone $childPageBuilder,
                    $this->fieldBuilder
                );

                if ($childPageInstance instanceof PageBaseContract) {
                    $childPageInstance->bind();
                }
            }
        }
    }

    /**
     * Return OptionsPage\Base instance
     *
     * @param $class
     * @param $pageBuilder
     * @param $fields
     *
     * @return null|object
     */
    protected function getOptionPageBaseInstance($class, $pageBuilder, $fields)
    {
        return class_exists($class) ? new $class($pageBuilder, $fields) : null;
    }
}