<?php

namespace Botble\District\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\District\Http\Requests\DistrictRequest;
use Botble\District\Models\District;
use Botble\Base\Http\Controllers\BaseController;
use Botble\District\Tables\DistrictTable;
use Botble\District\Forms\DistrictForm;

class DistrictController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/district::district.name')), route('district.index'));
    }

    public function index(DistrictTable $table)
    {
        $this->pageTitle(trans('plugins/district::district.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/district::district.create'));

        return DistrictForm::create()->renderForm();
    }

    public function store(DistrictRequest $request)
    {
        $form = DistrictForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('district.index'))
            ->setNextUrl(route('district.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(District $district)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $district->name]));

        return DistrictForm::createFromModel($district)->renderForm();
    }

    public function update(District $district, DistrictRequest $request)
    {
        DistrictForm::createFromModel($district)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('district.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(District $district)
    {
        return DeleteResourceAction::make($district);
    }
}
