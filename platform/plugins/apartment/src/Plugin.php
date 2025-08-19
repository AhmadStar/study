<?php

namespace Botble\Apartment;

use Illuminate\Support\Facades\Schema;
use Botble\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Schema::dropIfExists('Apartments');
        Schema::dropIfExists('Apartments_translations');
    }
}
