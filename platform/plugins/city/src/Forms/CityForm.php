<?php

namespace Botble\City\Forms;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FormAbstract;
use Botble\City\Http\Requests\CityRequest;
use Botble\City\Models\City;

class CityForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(City::class)
            ->setValidatorClass(CityRequest::class)
            ->add('name', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('اسم المدينة')
                ->required())
            ->add('country', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('الدولة'))
            ->add('population_estimate', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('التعداد التقديري'))
            ->add('status', \Botble\Base\Forms\Fields\SelectField::class, StatusFieldOption::make())
            ->setBreakFieldPoint('status');
    }
}
