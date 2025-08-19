<?php

namespace Botble\Family\Forms;


use Botble\Base\Forms\Fields\TextField;
use Botble\Member\Forms\Fronts\Auth\FieldOptions\TextFieldOption;
use Botble\Family\Http\Requests\RegisterFamilyRequest;
use Botble\Family\Models\Family;
use Botble\Theme\FormFront;
use Botble\Base\Forms\Fields\RepeaterField;
use Botble\Base\Forms\FieldOptions\RepeaterFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\DatePickerField;
use Botble\Person\Models\Person;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;

use function Termwind\style;

class RegisterFamilyForm extends FormFront
{
    public static function formTitle(): string
    {
        return trans('plugins/member::member.form.register_title');
    }

    public function setup(): void
    {

        $fields = [
            [
                'type' => 'text',
                'label' => __('الاسم'),
                'attributes' => [
                    'name' => 'name',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => __('أدخل الاسم'),
                    ],
                ],
            ],
            [
                'type' => 'text',
                'label' => __('تاريخ الميلاد'),
                'attributes' => [
                    'name' => 'birth_date',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control datepicker',
                        'placeholder' => __('تاريخ الميلاد'),
                    ],
                ],
            ],
            [
                'type' => 'text',
                'label' => __('الوظيفة'),
                'attributes' => [
                    'name' => 'job',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => __('أدخل الوظيفة'),
                    ],
                ],
            ],
            [
                'type' => 'text',
                'label' => __('الجنس'),
                'attributes' => [
                    'name' => 'gender',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => __('الجنس'),
                    ],
                ],
            ],
            [
                'type' => 'text',
                'label' => __('الحالة الاجتماعية'),
                'attributes' => [
                    'name' => 'marital_status',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => __('الحالة الاجتماعية'),
                    ],
                ],
            ],
            [
                'type' => 'text',
                'label' => __('المستوى التعليمي'),
                'attributes' => [
                    'name' => 'education_level',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => __('المستوى التعليمي'),
                    ],
                ],
            ],
            // [
            //     'type' => 'mediaImage',
            //     'label' => 'الصورة',
            //     'attributes' => [
            //         'name' => 'image',
            //         'value' => null,
            //     ],
            // ],
        ];

        $this
            ->setUrl(route('public.registerFamily.post'))
            ->setValidatorClass(RegisterFamilyRequest::class)
            ->model(Family::class)
            ->setFormOption('class', 'row')
            ->add(
                'intro_text',
                'html', // This is the "raw HTML" field type
                [
                    'html' => '<div class="mb-4"><p style="color:#000;text-align:justify">كل عائلة يتم تسجيلها ستستفيد بشكل مباشر عبر حصولها على معلومات حول المنح الدراسية والعلاج الطبي. كما سيساهم هذا التسجيل في التعرف على أفراد العائلة واحتياجاتهم بدقة أكبر، مما يتيح تقديم الدعم المناسب لهم بشكل فعال.

</p></div>',
                ]
            )
            ->add(
                'start_row',
                'html',
                [
                    'html' => '<div class="row">'
                ]
            )
            ->add(
                'family_number',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('رقم العائلة'))
                    ->placeholder(__('رقم العائلة'))
            )
            ->add(
                'father_name',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('اسم الاب'))
                    ->placeholder(__('اسم الاب'))
                    ->wrapperAttributes(['class' => 'col-md-6 mb-3'])
            )
            ->add(
                'mother_name',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('اسم الزوجة'))
                    ->placeholder(__('اسم الزوجة'))
                    ->wrapperAttributes(['class' => 'col-md-6  mb-3'])
            )
            ->add(
                'village',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('القرية'))
                    ->choices([
                        'حفر' => 'حفر',
                        'الرزانية' => 'الرزانية',
                        'عين عيشة' => 'عين عيشة',
                        'القادرية' => 'القادرية',
                        'العليقة' => 'العليقة',
                        'كفر نفاخ' => 'كفر نفاخ',
                        'نعران' => 'نعران',
                        'عين قرّة' => 'عين قرّة',
                        'ضبية' => 'ضبية',
                        'الحسينية' => 'الحسينية',
                        'المغيّر' => 'المغيّر',
                        'عين العلق' => 'عين العلق',
                        'الأحمدية' => 'الأحمدية',
                        'السنديانة' => 'السنديانة',
                        'عين السمسم' => 'عين السمسم',
                        'قريتي غير مذكورة' => 'قريتي غير مذكورة',
                    ])
                    ->placeholder(__('اختر القرية'))
                    ->wrapperAttributes(['class' => 'col-md-6 mb-3'])
            )

            ->add(
                'current_location',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('مكان السكن الحالي'))
                    ->placeholder(__('مكان السكن الحالي'))
                    ->wrapperAttributes(['class' => 'col-md-6  mb-3'])
            )
            ->add(
                'base_location',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('مكان السكن الاصلي'))
                    ->placeholder(__('مكان السكن الاصلي'))
                    ->wrapperAttributes(['class' => 'col-md-6  mb-3'])
            )
            ->add(
                'father_career',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('مهنة الأب'))
                    ->placeholder(__('مهنة الأب'))
                    ->wrapperAttributes(['class' => 'col-md-6  mb-3'])
            )
            ->add(
                'mother_career',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('مهنة الزوجة'))
                    ->placeholder(__('مهنة الزوجة'))
                    ->wrapperAttributes(['class' => 'col-md-6  mb-3'])
            )->add(
                'father_certificate',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('شهادة الأب الدراسية'))
                    ->placeholder(__('شهادة الأب'))
                    ->wrapperAttributes(['class' => 'col-md-6  mb-3'])
            )
            ->add(
                'mother_certificate',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('شهادة الزوجة الدراسية'))
                    ->placeholder(__('شهادة الزوجة'))
                    ->wrapperAttributes(['class' => 'col-md-6  mb-3'])
            )->add(
                'phone',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('رقم الموبايل (مع رمز البلد)'))
                    ->placeholder(__('رقم الموبايل (مع رمز البلد)'))
                    ->wrapperAttributes(['class' => 'col-md-6  mb-3'])
            )->add(
                'email',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('الايميل'))
                    ->placeholder(__('الايميل'))
                    ->wrapperAttributes(['class' => 'col-md-6  mb-3'])
            )->add(
                'martyrs_names',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('إذا كان لديك شهداء يرجى ذكر أسماءهم'))
                    ->placeholder(__('أدخل أسماء الشهداء هنا'))
                    ->wrapperAttributes(['class' => 'col-md-6 mb-3'])
            )
            ->add(
                'missing_names',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('إذا كان لديك مفقودين يرجى ذكر أسماءهم'))
                    ->placeholder(__('أدخل أسماء المفقودين هنا'))
                    ->wrapperAttributes(['class' => 'col-md-6 mb-3'])
            )
            ->add(
                'disabled_or_widows_names',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('إذا كان لديك حالات إعاقة أو أرامل يُرجى ذكر الأسماء ووضع كل شخص'))
                    ->placeholder(__('أدخل أسماء ذوي الإعاقة أو الأرامل هنا'))
                    ->wrapperAttributes(['class' => 'col-md-6 mb-3'])
            )
            ->add(
                'family_members',
                RepeaterField::class,
                RepeaterFieldOption::make()
                    ->label('أفراد العائلة')
                    ->value(setting('email_template_social_links', []))
                    ->fields($fields)
            )
            ->add(
                'end_row',
                'html',
                [
                    'html' => '</div>'
                ]
            );
    }
}
