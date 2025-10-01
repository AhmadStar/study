<?php

namespace Botble\Street\Forms;

use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Street\Http\Requests\StreetRequest;
use Botble\Street\Models\Street;
use Botble\Area\Models\Area;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\HiddenFieldOption;

class StreetForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Street::class)
            ->setValidatorClass(StreetRequest::class)
            ->add('area_id', \Botble\Base\Forms\Fields\SelectField::class, SelectFieldOption::make()
                ->label('القطاع')
                ->choices(Area::pluck('name', 'id')->toArray())
                ->required())
            ->add('name', TextField::class, NameFieldOption::make()->required())
            ->add('shape', \Botble\Base\Forms\Fields\HiddenField::class, HiddenFieldOption::make()) // <- stores GeoJSON
            ->add('status', SelectField::class, StatusFieldOption::make())
            ->setBreakFieldPoint('status');

            $this->addMetaBoxes([
            'shape-map' => [
                'title' => 'الخريطة (ارسم المضلع/المستطيل/الخط )',
                'content' => view('plugins/area::partials.shape-map', [
                    'initialShape' => $this->getModel()->shape ?? null,
                ])->render(),
                'priority' => 1,
            ],
        ]);
    }
}
