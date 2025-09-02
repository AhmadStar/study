<?php

namespace Botble\Person\Forms;

use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FormAbstract;
use Botble\Person\Http\Requests\PersonRequest;
use Botble\Person\Models\Person;
use Botble\Family\Models\Family;

class PersonForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Person::class)
            ->setValidatorClass(PersonRequest::class)
            ->add('family_id', \Botble\Base\Forms\Fields\SelectField::class, SelectFieldOption::make()
                ->label('العائلة')
                ->choices(Family::pluck('family_code', 'id')->toArray())
                ->required())
            ->add('first_name', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('الاسم الأول')
                ->required())
            ->add('last_name', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('اسم العائلة')
                ->required())
            ->add('gender', \Botble\Base\Forms\Fields\SelectField::class, SelectFieldOption::make()
                ->label('الجنس')
                ->choices([
                    'male' => 'ذكر',
                    'female' => 'أنثى',
                ])
                ->required())
            ->add('relationship', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('صلة القرابة'))
            ->add('occupation', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('الوظيفة'))
            ->add('education_level', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('المستوى التعليمي'))
            ->add('national_id', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('الرقم الوطني'))
            ->add('phone_number', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('رقم الهاتف'))
            ->add('email', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('البريد الإلكتروني'))
            ->add('status', \Botble\Base\Forms\Fields\SelectField::class, StatusFieldOption::make())
            ->setBreakFieldPoint('status');
    }
}
