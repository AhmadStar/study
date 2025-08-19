<?php

namespace Botble\District\Forms;

use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FormAbstract;
use Botble\District\Http\Requests\DistrictRequest;
use Botble\District\Models\District;
use Botble\City\Models\City;

class DistrictForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(District::class)
            ->setValidatorClass(DistrictRequest::class)
            ->add('city_id', \Botble\Base\Forms\Fields\SelectField::class, SelectFieldOption::make()
                ->label('المدينة')
                ->choices(City::pluck('name', 'id')->toArray())
                ->required())
            ->add('name', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('اسم الحي/المنطقة')
                ->required())
            ->add('population_estimate', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('التعداد التقديري'))
            ->add('status', \Botble\Base\Forms\Fields\SelectField::class, StatusFieldOption::make())
            ->setBreakFieldPoint('status');
    }
}
