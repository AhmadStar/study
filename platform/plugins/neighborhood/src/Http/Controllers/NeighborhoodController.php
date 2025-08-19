<?php

namespace Botble\Neighborhood\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Neighborhood\Http\Requests\NeighborhoodRequest;
use Botble\Neighborhood\Models\Neighborhood;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Neighborhood\Tables\NeighborhoodTable;
use Botble\Neighborhood\Forms\NeighborhoodForm;

class NeighborhoodController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/neighborhood::neighborhood.name')), route('neighborhood.index'));
    }

    public function index(NeighborhoodTable $table)
    {
        $this->pageTitle(trans('plugins/neighborhood::neighborhood.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/neighborhood::neighborhood.create'));

        return NeighborhoodForm::create()->renderForm();
    }

    public function store(NeighborhoodRequest $request)
    {
        $form = NeighborhoodForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('neighborhood.index'))
            ->setNextUrl(route('neighborhood.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Neighborhood $neighborhood)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $neighborhood->name]));

        return NeighborhoodForm::createFromModel($neighborhood)->renderForm();
    }

    public function update(Neighborhood $neighborhood, NeighborhoodRequest $request)
    {
        NeighborhoodForm::createFromModel($neighborhood)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('neighborhood.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Neighborhood $neighborhood)
    {
        return DeleteResourceAction::make($neighborhood);
    }
}
