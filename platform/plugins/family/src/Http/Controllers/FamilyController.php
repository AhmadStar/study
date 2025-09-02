<?php

namespace Botble\Family\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Building\Models\Building;
use Botble\Family\Http\Requests\FamilyRequest;
use Botble\Family\Models\Family;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Family\Tables\FamilyTable;
use Botble\Family\Forms\FamilyForm;
use Botble\SeoHelper\Facades\SeoHelper;
use Illuminate\Http\Request;
use Botble\Theme\Facades\Theme;
use Botble\Family\Http\Requests\RegisterFamilyRequest;
use Botble\Family\Forms\RegisterFamilyForm;
use Botble\Person\Models\Person;

class FamilyController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/family::family.name')), route('family.index'));
    }

    public function showRegistrationFamilyForm()
    {
        abort_unless(setting('member_enabled_registration', true), 404);

        SeoHelper::setTitle(__('Register Family'));

        if (! session()->has('url.intended')) {
            session(['url.intended' => url()->previous()]);
        }

        // Theme::breadcrumb()->add(__('Register Family'), route('public.registerFamily'));

        return Theme::scope(
            'plugins/member::themes.auth.register-family',
            ['form' => RegisterFamilyForm::create()],
            'plugins/member::themes.auth.register'
        )->render();
    }



public function registerFamily(RegisterFamilyRequest $request)
{
    // 1. Create the family
    $family = Family::create([
        'family_number' => $request->validated('family_number'),
        'father_name' => $request->validated('father_name'),
        'mother_name' => $request->validated('mother_name'),
        'village' => $request->validated('village'),
        'current_location' => $request->validated('current_location'),
        'base_location' => $request->validated('base_location'),
        'father_career' => $request->validated('father_career'),
        'address' => $request->validated('address'),
        'mother_career' => $request->validated('mother_career'),
        'father_certificate' => $request->validated('father_certificate'),
        'mother_certificate' => $request->validated('mother_certificate'),
        'phone' => $request->validated('phone'),
        'email' => $request->validated('email'),
        'family_members' => $request->validated('family_members'),
        'martyrs_names' => $request->validated('martyrs_names'),
        'missing_names' => $request->validated('missing_names'),
        'disabled_or_widows_names' => $request->validated('disabled_or_widows_names'),
        'status' => 'PUBLISHED',
    ]);

    // 2. Store family members
    $familyMembers = $request->input('family_members', []);

    foreach ($familyMembers as $memberData) {
        $memberAttributes = collect($memberData)->pluck('value', 'key')->toArray();

        // 🛑 Skip if both first_name and last_name are empty
        if (empty($memberAttributes['first_name']) && empty($memberAttributes['last_name'])) {
            continue;
        }

        $memberAttributes['family_id'] = $family->id;

        // Optional: remove unfillable fields like image if not needed
        unset($memberAttributes['image']);

        Person::create($memberAttributes);
    }

    return $this
        ->httpResponse()
        ->setMessage(trans('Family registered successfully!'));
}


    public function index(FamilyTable $table)
    {
        $this->pageTitle(trans('plugins/family::family.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/family::family.create'));

        return FamilyForm::create()->renderForm();
    }


    public function storeF(Request $request)
    {
        $data = $request->all();

        $validated = validator($data, [
            'name'                 => ['nullable', 'string', 'max:255'],
            'family_name'          => ['required', 'string', 'max:255'],
            'family_number'        => ['nullable', 'string', 'max:255'],
            'floor_number'         => ['nullable', 'integer', 'min:0'],
            'family_code'          => ['nullable', 'in:A,B,C,D,E'],
            'phone'                => ['nullable', 'string', 'max:50'],
            'count_family_members' => ['nullable', 'integer', 'min:0'],
            'house_type'           => ['nullable', 'in:ملك,ايجار,غير ذلك'],
            'status'               => ['required', 'in:published,draft,pending'],
            'address'              => ['nullable', 'string', 'max:255'],
            'notes'                => ['nullable', 'string'],
            'building_id'          => ['required', 'exists:buildings,id'],

            'is_featured_person'   => ['sometimes', 'boolean'],
            'featured_person'      => ['nullable', 'string', 'max:255', 'required_if:is_featured_person,1'],

            'is_empty'   => ['sometimes', 'boolean'],
            'need_review'   => ['sometimes', 'boolean'],
            'head_name'            => ['nullable','string','max:255'],     // إسـم رب الأسرة
            'nationality'          => ['nullable','string','max:255'],     // الجنسية
            'birth_place'          => ['nullable','string','max:255'],     // مكان الولادة
            'birth_date'           => ['nullable','date','before:tomorrow'],// تاريخ الولادة
            'civil_registry'       => ['nullable','string','max:255'],     // القيد
            'national_id'          => ['nullable','string','max:255'],     // الرقم الوطني
            'father_occupation'    => ['nullable','string','max:255'],     // مهنة الأب
        ])->validate();

        $validated['is_featured_person'] = $request->boolean('is_featured_person');

        if (!$validated['is_featured_person']) {
            $validated['featured_person'] = null;
        }

        $family = Family::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'error'  => false,
                'message'=> 'Family created successfully.',
                'result' => ['data' => $family],
            ]);
        }

        return redirect()->back()->with('status', 'تم إنشاء العائلة بنجاح');
    }

    public function store(FamilyRequest $request)
    {
        $form = FamilyForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('family.index'))
            ->setNextUrl(route('family.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Family $family)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $family->name]));

        return FamilyForm::createFromModel($family)->renderForm();
    }

    public function update(Family $family, FamilyRequest $request)
    {
        FamilyForm::createFromModel($family)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('family.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Family $family)
    {
        return DeleteResourceAction::make($family);
    }
}
