<?php

namespace App\Http\Controllers\Concerns;

use App\Services\BreadcrumbService;

trait HasBreadcrumbs
{
    protected $breadcrumbService;

    /**
     * Initialize breadcrumb service
     */
    protected function initBreadcrumbs(): BreadcrumbService
    {
        if (!$this->breadcrumbService) {
            $this->breadcrumbService = new BreadcrumbService();
        }

        return $this->breadcrumbService;
    }

    /**
     * Set breadcrumbs for common pages
     *
     * @param string $page
     * @param array $params
     * @return array
     */
    protected function setBreadcrumbs(string $page, array $params = []): array
    {
        $breadcrumbs = $this->initBreadcrumbs()->setForPage($page, $params)->getBreadcrumbs();
        
        return compact('breadcrumbs');
    }

    /**
     * Add custom breadcrumb
     *
     * @param string $title
     * @param string|null $url
     * @param bool $isCurrent
     * @return array
     */
    protected function addBreadcrumb(string $title, ?string $url = null, bool $isCurrent = false): array
    {
        $breadcrumbs = $this->initBreadcrumbs()->add($title, $url, $isCurrent)->getBreadcrumbs();
        
        return compact('breadcrumbs');
    }

    /**
     * Add multiple breadcrumbs
     *
     * @param array $items
     * @return array
     */
    protected function addMultipleBreadcrumbs(array $items): array
    {
        $breadcrumbs = $this->initBreadcrumbs()->addMultiple($items)->getBreadcrumbs();
        
        return compact('breadcrumbs');
    }
}
