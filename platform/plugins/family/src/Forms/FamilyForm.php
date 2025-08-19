<?php

namespace Botble\Family\Forms;

use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FormAbstract;
use Botble\Family\Http\Requests\FamilyRequest;
use Botble\Family\Models\Family;
use Botble\Apartment\Models\Apartment;

class FamilyForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Family::class)
            ->setValidatorClass(FamilyRequest::class)
            ->add('apartment_id', \Botble\Base\Forms\Fields\SelectField::class, SelectFieldOption::make()
                ->label('الشقة')
                ->choices(Apartment::pluck('apartment_number', 'id')->toArray())
                ->required())
            ->add('family_code', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('رمز العائلة')
                ->required())
            ->add('notes', \Botble\Base\Forms\Fields\TextareaField::class, TextareaFieldOption::make()
                ->label('ملاحظات'))
            ->add('status', \Botble\Base\Forms\Fields\SelectField::class, StatusFieldOption::make())
            ->setBreakFieldPoint('status');
    }
}
