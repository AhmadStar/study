<?php

namespace Botble\Building\Forms;

use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FormAbstract;
use Botble\Building\Http\Requests\BuildingRequest;
use Botble\Building\Models\Building;
use Botble\Area\Models\Area;

class BuildingForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Building::class)
            ->setValidatorClass(BuildingRequest::class)

            ->add('name', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('اسم المبنى')
                ->required())

            ->add('area_id', \Botble\Base\Forms\Fields\SelectField::class, SelectFieldOption::make()
                ->label('المنطقة')
                ->choices(Area::pluck('name', 'id')->toArray())
                ->required())

            ->add('building_number', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('رقم المبنى'))

            ->add('floors_count', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('عدد الطوابق'))

            ->add('latitude', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('خط العرض'))

            ->add('longitude', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('خط الطول'))

            ->add('address', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('العنوان'))

            ->add('description', \Botble\Base\Forms\Fields\TextareaField::class, TextFieldOption::make()
                ->label('الوصف'))

            ->add('status', \Botble\Base\Forms\Fields\SelectField::class, StatusFieldOption::make())

            ->setBreakFieldPoint('status');
    }
}
