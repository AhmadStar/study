<?php

namespace Botble\Neighborhood\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Base\Facades\DashboardMenu;
use Botble\Neighborhood\Models\Neighborhood;

class NeighborhoodServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/neighborhood')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadMigrations();
            
            if (defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')) {
                \Botble\LanguageAdvanced\Supports\LanguageAdvancedManager::registerModule(Neighborhood::class, [
                    'name',
                ]);
            }
            
            DashboardMenu::default()->beforeRetrieving(function () {
                DashboardMenu::registerItem([
                    'id' => 'cms-plugins-neighborhood',
                    'priority' => 5,
                    'parent_id' => null,
                    'name' => 'plugins/neighborhood::neighborhood.name',
                    'icon' => 'ti ti-box',
                    'url' => route('neighborhood.index'),
                    'permissions' => ['neighborhood.index'],
                ]);
            });
    }
}
