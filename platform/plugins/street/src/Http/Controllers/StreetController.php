<?php

namespace Botble\Street\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Street\Http\Requests\StreetRequest;
use Botble\Street\Models\Street;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Street\Tables\StreetTable;
use Botble\Street\Forms\StreetForm;

class StreetController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/street::street.name')), route('street.index'));
    }

    public function index(StreetTable $table)
    {
        $this->pageTitle(trans('plugins/street::street.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/street::street.create'));

        return StreetForm::create()->renderForm();
    }

    public function store(StreetRequest $request)
    {
        $form = StreetForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('street.index'))
            ->setNextUrl(route('street.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Street $street)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $street->name]));

        return StreetForm::createFromModel($street)->renderForm();
    }

    public function update(Street $street, StreetRequest $request)
    {
        StreetForm::createFromModel($street)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('street.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Street $street)
    {
        return DeleteResourceAction::make($street);
    }
}
