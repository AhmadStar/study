<?php

namespace Botble\Apartment\Forms;

use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FormAbstract;
use Botble\Apartment\Http\Requests\ApartmentRequest;
use Botble\Apartment\Models\Apartment;
use Botble\Building\Models\Building;

class ApartmentForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Apartment::class)
            ->setValidatorClass(ApartmentRequest::class)
            ->add('building_id', \Botble\Base\Forms\Fields\SelectField::class, SelectFieldOption::make()
                ->label('المبنى')
                ->choices(Building::pluck('building_number', 'id')->toArray())
                ->required())
            ->add('apartment_number', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('رقم الشقة'))
            ->add('floor_number', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('رقم الطابق'))
            ->add('status', \Botble\Base\Forms\Fields\SelectField::class, StatusFieldOption::make())
            ->setBreakFieldPoint('status');
    }
}
