<?php

namespace Botble\Neighborhood;

use Illuminate\Support\Facades\Schema;
use Botble\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Schema::dropIfExists('Neighborhoods');
        Schema::dropIfExists('Neighborhoods_translations');
    }
}
