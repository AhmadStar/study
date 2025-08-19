<?php

namespace Botble\City\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\City\Http\Requests\CityRequest;
use Botble\City\Models\City;
use Botble\Base\Http\Controllers\BaseController;
use Botble\City\Tables\CityTable;
use Botble\City\Forms\CityForm;

class CityController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/city::city.name')), route('city.index'));
    }

    public function index(CityTable $table)
    {
        $this->pageTitle(trans('plugins/city::city.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/city::city.create'));

        return CityForm::create()->renderForm();
    }

    public function store(CityRequest $request)
    {
        $form = CityForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('city.index'))
            ->setNextUrl(route('city.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(City $city)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $city->name]));

        return CityForm::createFromModel($city)->renderForm();
    }

    public function update(City $city, CityRequest $request)
    {
        CityForm::createFromModel($city)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('city.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(City $city)
    {
        return DeleteResourceAction::make($city);
    }
}
