<?php

namespace Botble\Area\Forms;

use Botble\Base\Forms\FieldOptions\HiddenFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FormAbstract;
use Botble\Area\Http\Requests\AreaRequest;
use Botble\Area\Models\Area;
use Botble\District\Models\District;

class AreaForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Area::class)
            ->setValidatorClass(AreaRequest::class)
            ->add('district_id', \Botble\Base\Forms\Fields\SelectField::class, SelectFieldOption::make()
                ->label('الحي/المنطقة')
                ->choices(District::pluck('name', 'id')->toArray())
                ->required())
            ->add('name', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('اسم المنطقة الفرعية')
                ->required())
            ->add('shape', \Botble\Base\Forms\Fields\HiddenField::class, HiddenFieldOption::make()) // <- stores GeoJSON
            ->add('status', \Botble\Base\Forms\Fields\SelectField::class, StatusFieldOption::make())
            ->setBreakFieldPoint('status');
        // Inject map UI under the form (depends on your setup):
        $this->addMetaBoxes([
            'shape-map' => [
                'title' => 'الخريطة (ارسم المضلع/المستطيل)',
                'content' => view('plugins/area::partials.shape-map', [
                    'initialShape' => $this->getModel()->shape ?? null,
                ])->render(),
                'priority' => 1,
            ],
        ]);
    }
}
