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

            ->add('family_name', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('اسم العائلة')
                ->required())

            ->add('family_number', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('رقم العائلة')
                ->required())

            ->add('floor_number', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('رقم الطابق'))

            ->add('apartment_id', \Botble\Base\Forms\Fields\SelectField::class, SelectFieldOption::make()
                ->label('الشقة')
                ->choices(Apartment::pluck('apartment_number', 'id')->toArray()))

            ->add('family_code', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('رمز العائلة'))

            ->add('address', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('العنوان'))

            ->add('region_id', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('المنطقة'))

            ->add('phone', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('رقم الهاتف'))

            ->add('count_family_members', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('عدد أفراد العائلة'))

            ->add('building_id', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('معرف البناء'))

            ->add('house_type', \Botble\Base\Forms\Fields\SelectField::class, SelectFieldOption::make()
                ->label('نوع ملكية العقار')
                ->choices([
                    'ملك' => 'ملك',
                    'ايجار' => 'ايجار',
                    'غير ذلك' => 'غير ذلك',
                ]))

            ->add('notes', \Botble\Base\Forms\Fields\TextareaField::class, TextareaFieldOption::make()
                ->label('ملاحظات'))

            ->add('status', \Botble\Base\Forms\Fields\SelectField::class, StatusFieldOption::make())

            ->setBreakFieldPoint('status');
    }
}
