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
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Family\Repositories\Interfaces\FamilyInterface;


class FamilyController extends BaseController
{
    /**
     * @var familypoInterface
     */
    protected $familyRepository;

    public function __construct(FamilyInterface $familyRepository,)
    {

        $this->familyRepository = $familyRepository;

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
        'birth_date' => $request->validated('birth_date'),
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
        'created_by' => auth()->user()->name
    ]);

    // 2. Store family members
    $familyMembers = $request->input('family_members', []);

    if (is_string($familyMembers)) {
        $familyMembers = json_decode($familyMembers, true) ?? [];
    }

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

    public function store(FamilyRequest $request)
{
    $request->merge([
    'created_by' => auth()->user()->name,
]);

    $form = FamilyForm::create()->setRequest($request);

    // 1️⃣ خزن كل شيء بدون family_members
    $data = $request->except('family_members');
    $data['created_by'] = auth()->user()->name;

    // 2️⃣ حول أي حقل array إلى نص إذا كان هناك حاجة
    if (isset($data['civil_registry']) && is_array($data['civil_registry'])) {
        $data['civil_registry'] = implode(', ', $data['civil_registry']);
    }

    // 3️⃣ احفظ العائلة
    $family = $form->save($data);

    // 4️⃣ خزن عناصر العائلة في جدول Person
    $familyMembers = $request->input('family_members', []);

    if (is_string($familyMembers)) {
        $familyMembers = json_decode($familyMembers, true) ?? [];
    }

    foreach ($familyMembers as $memberData) {
        $memberAttributes = collect($memberData)->pluck('value', 'key')->toArray();

        // تخطي إذا الاسم فارغ
        if (empty($memberAttributes['full_name'])) {
            continue;
        }

        $memberAttributes['family_id'] = $family->id;

        unset($memberAttributes['image']); // إذا هناك حقل صورة غير مرغوب
        Person::create($memberAttributes);
    }

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

    // 🟢 تحديث أفراد العائلة
    $familyMembers = $request->input('family_members', []);

    if (is_string($familyMembers)) {
        $familyMembers = json_decode($familyMembers, true) ?? [];
    }

    // IDs الموجودة في الطلب
    $requestIds = collect($familyMembers)
        ->pluck('value', 'key')
        ->map(fn($v, $k) => $k === 'id' ? $v : null)
        ->filter()
        ->values()
        ->toArray();

    // حذف الأعضاء غير الموجودين في الطلب
    Person::where('family_id', $family->id)
        ->whereNotIn('id', $requestIds)
        ->delete();

    foreach ($familyMembers as $memberData) {
        $memberAttributes = collect($memberData)->pluck('value', 'key')->toArray();

        // تخطي إذا الاسم فارغ
        if (empty($memberAttributes['full_name'])) {
            continue;
        }

        $memberAttributes['family_id'] = $family->id;
        unset($memberAttributes['image']); // إذا هناك حقل صورة غير مرغوب

        if (!empty($memberAttributes['id'])) {
            // 🟢 تحديث العضو
            $person = Person::where('id', $memberAttributes['id'])
                ->where('family_id', $family->id)
                ->first();

            if ($person) {
                $person->update($memberAttributes);
            }
        } else {
            // 🟢 إضافة عضو جديد
            Person::create($memberAttributes);
        }
    }

    return $this
        ->httpResponse()
        ->setPreviousUrl(route('family.index'))
        ->setMessage(trans('core/base::notices.update_success_message'));
}


    public function destroy(Family $family)
    {
        return DeleteResourceAction::make($family);
    }

    public function getBuildingsByArea(Request $request)
{
    $areaId = $request->input('area_id');

    $buildings = Building::where('area_id', $areaId)
        ->get(['id', 'area_id', 'street', 'name']); // choose the fields you need

    return response()->json($buildings);
}

public function familyList()
    {
        page_title()->setTitle(trans('plugins/report::report.name'));

        \Assets::addStylesDirectly([
            'https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css',
            'https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css',
            'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css',
        ]);

        \Assets::addScriptsDirectly([
            'https://code.jquery.com/jquery-3.6.4.min.js',
            'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js',
            'https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js',
            'https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js',
            //   'cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.4/jszip.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js',
            '/themes/ripple/js/report.js',
        ]);

        // get suppliers and other data
        // $suppliers = app(Suppliers::class)->pluck('name', 'id');
        // $users = app(User::class)->pluck('username', 'id');
        // $familyCategories = $this->familyCategoryRepository->all();
        // $storeRepository = app(StoreInterface::class);
        // $allStores = $storeRepository->all();

        // $categories = [];

        // foreach ($familyCategories as $category) {
        //     $categories[] = [
        //         'id'   => $category->id,
        //         'name' => $category->name,
        //     ];
        // }

        // $stores = [];

        // foreach ($allStores as $store) {
        //     $stores[] = [
        //         'id'   => $store->id,
        //         'name' => $store->name,
        //     ];
        // }


        return view('plugins/family::index', [
            'store' => 'store test data',
            // 'suppliers' => $suppliers,
            // 'categories' => $categories,
            // 'stores' => $stores,
            // 'users' => $users,
        ]);
    }

public function familyFilterList(Request $request, BaseHttpResponse $response)
    {
        $store_id = $request->input('store_id');
        $filters = $request->input('filters');
        $filters['suppliers'] = ($request->has('suppliers')) ? $request->input('suppliers') : [];
        $filters['categoriesFilter'] = ($request->has('categories')) ? $request->input('categories') : [];
        $filters['stores'] = ($request->has('stores')) ? $request->input('stores') : [];
        $filters['keyword'] = ($request->has('keyword')) ? $request->input('keyword') : '';
        $filters['showActive'] = ($request->has('showActive')) ? $request->input('showActive') : [];
        $filters['showOnline'] = ($request->has('showOnline')) ? $request->input('showOnline') : [];
        $filters['showImages'] = ($request->has('showImages')) ? $request->input('showImages') : [];
        $filters['emptyName'] = ($request->has('emptyName')) ? $request->input('emptyName') : [];
        $filters['wrongPrice'] = ($request->has('wrongPrice')) ? $request->input('wrongPrice') : [];
        $filters['wrongBarcode'] = ($request->has('wrongBarcode')) ? $request->input('wrongBarcode') : [];
        $filters['discontinued'] = ($request->has('discontinued')) ? $request->input('discontinued') : [];
        $filters['showQuantities'] = ($request->has('showQuantities')) ? $request->input('showQuantities') : [];
        $filters['importedLocale'] = ($request->has('importedLocale')) ? $request->input('importedLocale') : [];
        $filters['showLastUpdated'] = ($request->has('showLastUpdated')) ? $request->input('showLastUpdated') : [];
        $filters['lastUpdatedType'] = ($request->has('lastUpdatedType')) ? $request->input('lastUpdatedType') : [];
        $filters['startDate'] = ($request->has('startDate')) ? $request->input('startDate') : [];
        $filters['endDate'] = ($request->has('endDate')) ? $request->input('endDate') : [];
        $filters['users'] = ($request->has('users')) ? $request->input('users') : [];

        $filters['first_loop'] = ($request->has('first_loop')) ? $request->input('first_loop') : [];

        $length = ($request->has('length')) ? $request->input('length') : 10;
        $current_page = ($request->has('current_page')) ? $request->input('current_page') : 1;

        // $dir = $request->input('order')[0]['dir'];
        $order = [];

        // foreach ($request->input('columns') as $index => $column) {
        //     if ($request->input('order')[0]['column'] == $index) {
        //         // Concatenate the table name to the field name
        //         $columnName = $column['data'];

        //         // Store the concatenated column name and direction in the order array
        //         $order[$columnName] = $dir;
        //     }
        // }

        $params = [
            'filters' => $filters,
            'condition' => [],
            'order_by' => $order,
            'take' => null,
            'paginate' => [
                'per_page' => $length,
                'current_page' => $current_page,
            ],
            'export' => false
        ];
        if ($request->has('init_qty')) {
            $params['condition'] = [['init_qty', '>', 0]];
        }

        $data = $this->familyRepository->getFamilyFilter($params);
        return response()->json($data);
    }

}
