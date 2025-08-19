<?php

namespace Botble\Building\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Building\Http\Requests\BuildingRequest;
use Botble\Building\Models\Building;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Building\Tables\BuildingTable;
use Botble\Building\Forms\BuildingForm;

class BuildingController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/building::building.name')), route('building.index'));
    }

    public function index(BuildingTable $table)
    {
        $this->pageTitle(trans('plugins/building::building.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/building::building.create'));

        return BuildingForm::create()->renderForm();
    }

    public function store(BuildingRequest $request)
    {
        $form = BuildingForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('building.index'))
            ->setNextUrl(route('building.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Building $building)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $building->name]));

        return BuildingForm::createFromModel($building)->renderForm();
    }

    public function update(Building $building, BuildingRequest $request)
    {
        BuildingForm::createFromModel($building)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('building.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Building $building)
    {
        return DeleteResourceAction::make($building);
    }
}
