<?php

namespace Armincms\Tools\ToolbarAction;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class ToolbarAction extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('armincms-tools-toolbar-action', __DIR__.'/../dist/js/tool.js');
        // Nova::style('armincms-tools-toolbar-action', __DIR__.'/../dist/css/tool.css');
    } 
}
