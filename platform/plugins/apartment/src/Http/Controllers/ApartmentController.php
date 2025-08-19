<?php

namespace Botble\Apartment\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Apartment\Http\Requests\ApartmentRequest;
use Botble\Apartment\Models\Apartment;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Apartment\Tables\ApartmentTable;
use Botble\Apartment\Forms\ApartmentForm;

class ApartmentController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/apartment::apartment.name')), route('apartment.index'));
    }

    public function index(ApartmentTable $table)
    {
        $this->pageTitle(trans('plugins/apartment::apartment.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/apartment::apartment.create'));

        return ApartmentForm::create()->renderForm();
    }

    public function store(ApartmentRequest $request)
    {
        $form = ApartmentForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('apartment.index'))
            ->setNextUrl(route('apartment.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Apartment $apartment)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $apartment->name]));

        return ApartmentForm::createFromModel($apartment)->renderForm();
    }

    public function update(Apartment $apartment, ApartmentRequest $request)
    {
        ApartmentForm::createFromModel($apartment)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('apartment.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Apartment $apartment)
    {
        return DeleteResourceAction::make($apartment);
    }
}
