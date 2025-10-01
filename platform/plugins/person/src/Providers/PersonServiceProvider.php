<?php

namespace Botble\Person\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Base\Facades\DashboardMenu;
use Botble\Person\Models\Person;

class PersonServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/person')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadMigrations();

            if (defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')) {
                \Botble\LanguageAdvanced\Supports\LanguageAdvancedManager::registerModule(Person::class, [
                    'name',
                ]);
            }

            DashboardMenu::default()->beforeRetrieving(function () {
                DashboardMenu::registerItem([
                    'id' => 'cms-plugins-person',
                    'priority' => 5,
                    'parent_id' => null,
                    'name' => 'plugins/person::person.name',
                    'icon' => 'ti ti-box',
                    'url' => route('person.index'),
                    'permissions' => ['person.index'],
                ]);
                DashboardMenu::registerItem([
                    'id' => 'cms-plugins-person',
                    'priority' => 4,
                    'parent_id' => null,
                    'name' => 'الخريطة التفاعلية',
                    'icon' => 'ti ti-box',
                    'url' => route('neighborhood.map'),
                    'permissions' => ['person.index'],
                ]);
            });
    }
}
