<?php

namespace App\Http\Controllers;

use App\Helpers\TitleHelper;

abstract class BaseController extends Controller
{
    /**
     * Generate dynamic page title based on controller and action
     */
    protected function getPageTitle($action = null): string
    {
        return TitleHelper::generateWithAction(static::class, $action);
    }
    
    /**
     * Return view with dynamic title
     */
    protected function viewWithTitle($view, $data = [], $action = null)
    {
        $pageTitle = $this->getPageTitle($action);
        return view($view, $data)->with('pageTitle', $pageTitle);
    }
}
