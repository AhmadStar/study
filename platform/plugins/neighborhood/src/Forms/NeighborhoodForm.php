<?php

namespace Botble\Neighborhood\Forms;

use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Neighborhood\Http\Requests\NeighborhoodRequest;
use Botble\Neighborhood\Models\Neighborhood;

class NeighborhoodForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Neighborhood::class)
            ->setValidatorClass(NeighborhoodRequest::class)
            ->add('name', TextField::class, NameFieldOption::make()->required())
            ->add('status', SelectField::class, StatusFieldOption::make())
            ->setBreakFieldPoint('status');
    }
}
