<?php

namespace Botble\Area\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Area\Http\Requests\AreaRequest;
use Botble\Area\Models\Area;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Area\Tables\AreaTable;
use Botble\Area\Forms\AreaForm;
use Illuminate\Http\JsonResponse;
class AreaController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/area::area.name')), route('area.index'));
    }




    public function getList(): JsonResponse
    {
        // Read from the model and return minimal fields for the dropdown
        $areas = Area::query()
            ->select(['id', 'name'])
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->orderBy('name')
            ->get();

        // Return as a plain array: [{id, name}, ...]
        return response()->json($areas);
    }

    public function index(AreaTable $table)
    {
        $this->pageTitle(trans('plugins/area::area.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/area::area.create'));

        return AreaForm::create()->renderForm();
    }

    public function store(AreaRequest $request)
    {
        $form = AreaForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('area.index'))
            ->setNextUrl(route('area.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Area $area)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $area->name]));

        return AreaForm::createFromModel($area)->renderForm();
    }

    public function update(Area $area, AreaRequest $request)
    {
        AreaForm::createFromModel($area)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('area.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Area $area)
    {
        return DeleteResourceAction::make($area);
    }
}
