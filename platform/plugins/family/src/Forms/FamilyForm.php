<?php

namespace Botble\Family\Forms;

use Botble\Area\Models\Area;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FormAbstract;
use Botble\Family\Http\Requests\FamilyRequest;
use Botble\Family\Models\Family;
use Botble\Building\Models\Building;
use Botble\Base\Forms\Fields\RepeaterField;
use Botble\Base\Forms\FieldOptions\RepeaterFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\Fields\OnOffField;

class FamilyForm extends FormAbstract
{
    public function setup(): void
    {

        $fields = [
            [
                'type' => 'text',
                'label' => __('الاسم'),
                'attributes' => [
                    'name' => 'full_name',
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
                    'value' => now()->format('Y-m-d'),
                    'options' => [
                        'class' => 'form-control datepicker',
                        'placeholder' => __('تاريخ الميلاد'),
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
                'label' => __('المهنة'),
                'attributes' => [
                    'name' => 'job',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => __('المهنة'),
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
        ];

        $this->add('custom_css', \Botble\Base\Forms\Fields\HtmlField::class, [
            'html' => '<style>
                .half-width {
                    display: inline-block;
                    width: 49%;
                    vertical-align: top;
                }
                .half-width:nth-child(2n) {
                    margin-right: 0;
                }
                .third-width {
                    display: inline-block;
                    width: 32%;
                    vertical-align: top;
                }
                .third-width:nth-child(3n) {
                    margin-right: 0;
                }
                .mar-3{
                    margin-right:1% !important;
                }


            </style>'
        ]);

        $this->add('custom_js', \Botble\Base\Forms\Fields\HtmlField::class, [
    'html' => '<script>
        $(document).ready(function () {
            let $areaSelect = $(\'select[name="region_id"]\');
            let areaName = $areaSelect.find("option:selected").text();
            let $buildingSelect = $(\'select[name="building_id"]\');

            let selectedBuildingId = "' . ($this->getModel()->building_id ?? '') . '";

            function loadBuildings(areaId) {
                $buildingSelect.empty();
                if (areaId) {
                    $.get("' . route('ajax.buildings.by-area') . '", { area_id: areaId }, function (data) {
                        $.each(data, function (index, building) {

                            console.log(areaName);

                            let optionText = areaName + " " + building.street + " / " + building.name;
                            let option = new Option(optionText, building.id);

                            if (building.id == selectedBuildingId) {
                                option.selected = true; // pre-select on edit
                            }

                            $buildingSelect.append(option);
                        });
                        $buildingSelect.trigger(\'change\');
                    });
                }
            }

            $areaSelect.on(\'change\', function () {
                loadBuildings($(this).val());
            });

            if ($areaSelect.val()) {
                loadBuildings($areaSelect.val());
            }
        });
    </script>'
]);


        $this
            ->model(Family::class)
            ->setValidatorClass(FamilyRequest::class)
            ->add('smember_name', \Botble\Base\Forms\Fields\TextField::class, [
                'label' => 'اسم عنصر الدراسات',
                'required' => true,
                'wrapper' => ['class' => 'half-width']
            ])
            ->add('region_id', 'customSelect', [
                'label' => 'القطاع',
                'choices' => Area::pluck('name', 'id')->toArray(),
                'searchable' => true,
                'required' => true,
                'wrapper' => ['class' => 'half-width mb-3'],
            ])
            ->add('building_id', 'customSelect', [
                'label' => 'البناء',
                'choices' => [], // will be filled dynamically
                'searchable' => true,
                'required' => true,
                'wrapper' => ['class' => 'half-width'],
            ])
            ->add('cat', \Botble\Base\Forms\Fields\TextField::class, [
                'label' => 'الطابق',
                'required' => true,
                'wrapper' => ['class' => 'half-width mb-3']
            ])
            ->add('house_number', \Botble\Base\Forms\Fields\TextField::class, [
                'label' => 'رقم الشقة',
                'required' => true,
                'wrapper' => ['class' => 'half-width']
            ])
            ->add('custom_br', \Botble\Base\Forms\Fields\HtmlField::class, [
                'html' => '<hr>'
            ])
            ->add('head_name', \Botble\Base\Forms\Fields\TextField::class, [
                'label' => 'اسم رب الأسرة',
                'required' => true,
                'wrapper' => ['class' => 'half-width']
            ])
            ->add('family_name', \Botble\Base\Forms\Fields\TextField::class, [
                'label' => 'الكنية(العائلة)',
                'required' => true,
                'wrapper' => ['class' => 'half-width']
            ])

            ->add('custom_br1', \Botble\Base\Forms\Fields\HtmlField::class, [
                'html' => '<br>'
            ])
            ->add('nationality', \Botble\Base\Forms\Fields\TextField::class, [
                'label' => 'الجنسية',
                'required' => true,
                'wrapper' => ['class' => 'third-width']
            ])
            ->add('birth_place', \Botble\Base\Forms\Fields\TextField::class, [
                'label' => 'مكان الولادة',
                'required' => true,
                'wrapper' => ['class' => 'third-width']
            ])
            ->add('birth_date', \Botble\Base\Forms\Fields\TextField::class, [
                'label'    => 'تاريخ الولادة',
                'default_value' => now()->format('Y-m-d'),
                'wrapper'  => ['class' => 'third-width mb-3'],
            ])
            ->add('civil_registry', \Botble\Base\Forms\Fields\TextField::class, [
                'label' => 'القيد',
                'required' => true,
                'wrapper' => ['class' => 'half-width']
            ])
            ->add('national_id', \Botble\Base\Forms\Fields\TextField::class, [
                'label' => 'الرقم الوطني',
                'required' => true,
                'wrapper' => ['class' => 'half-width mb-3']
            ])
            ->add('career', \Botble\Base\Forms\Fields\TextField::class, [
                'label' => 'مهنة الأب',
                'wrapper' => ['class' => 'half-width']
            ])
            ->add('count_family_members', \Botble\Base\Forms\Fields\TextField::class, [
                'label' => 'عدد أفراد العائلة',
                'wrapper' => ['class' => 'half-width mb-3']
            ])
            ->add(
                'family_members',
                RepeaterField::class,
                RepeaterFieldOption::make()
                    ->label('أفراد العائلة')
                    ->value(setting('email_template_social_links', []))
                    ->fields($fields)
            )
            ->add('family_code', \Botble\Base\Forms\Fields\SelectField::class, [
                'label' => 'رمز العائلة',
                'choices' => [
                    's' => 'S',
                    'sh' => 'SH',
                    'a' => 'A',
                    'd' => 'D',
                    'i' => 'I',
                    'm' => 'M',
                    'ch' => 'CH',
                ],
                'wrapper' => ['class' => 'third-width'],
                'required' => true,
            ])
            ->add('phone', \Botble\Base\Forms\Fields\TextField::class, [
                'label' => 'رقم الهاتف',
                'wrapper' => ['class' => 'third-width']
            ])
            ->add('mobile', \Botble\Base\Forms\Fields\TextField::class, [
                'label' => 'موبايل',
                'wrapper' => ['class' => 'third-width mb-3']
            ])
            ->add('house_type', \Botble\Base\Forms\Fields\SelectField::class, [
                'label' => 'نوع ملكية العقار',
                'choices' => [
                    'ملك' => 'ملك',
                    'ايجار' => 'ايجار',
                    'غير ذلك' => 'غير ذلك',
                ],
                'wrapper' => ['class' => 'third-width']
            ])
            ->add('car', \Botble\Base\Forms\Fields\TextField::class, [
                'label' => 'رقم السيارة',
                'wrapper' => ['class' => 'third-width']
            ])
            ->add('weapon', \Botble\Base\Forms\Fields\TextField::class, [
                'label' => 'رقم السلاح',
                'wrapper' => ['class' => 'third-width mb-3']
            ])
            ->add('notes', \Botble\Base\Forms\Fields\TextareaField::class, [
                'label' => 'ملاحظات',
                'wrapper' => ['class' => 'col-12 mb-3']
            ])
            ->add(
                'security_review',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label('للمراجعة الأمنية')
            )
            ->add('status', \Botble\Base\Forms\Fields\SelectField::class, StatusFieldOption::make()->toArray() + [
                'wrapper' => ['class' => 'col-12']
            ])
            ->setBreakFieldPoint('status');
    }
}
