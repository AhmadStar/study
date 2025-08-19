<?php

namespace Botble\Person\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Person\Http\Requests\PersonRequest;
use Botble\Person\Models\Person;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Person\Tables\PersonTable;
use Botble\Person\Forms\PersonForm;

class PersonController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/person::person.name')), route('person.index'));
    }

    public function index(PersonTable $table)
    {
        $this->pageTitle(trans('plugins/person::person.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/person::person.create'));

        return PersonForm::create()->renderForm();
    }

    public function store(PersonRequest $request)
    {
        $form = PersonForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('person.index'))
            ->setNextUrl(route('person.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Person $person)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $person->name]));

        return PersonForm::createFromModel($person)->renderForm();
    }

    public function update(Person $person, PersonRequest $request)
    {
        PersonForm::createFromModel($person)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('person.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Person $person)
    {
        return DeleteResourceAction::make($person);
    }
}
