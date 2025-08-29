<?php

namespace Botble\Building\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Building\Models\Building;
use Botble\Person\Models\Person;
use Botble\Family\Models\Family;
use Illuminate\Http\Request;

class FrontendController extends BaseController
{
    public function map()
    {
        $buildings = Building::with('persons')->get();

        return view('plugins/building::map', ['buildings' => $buildings])->render();
    }

    public function getResidents($id)
    {
        $building = Building::with('persons')->findOrFail($id);
        $building->building_type= $building->getOriginal('building_type');//arabic translation
        $building->building_type_label= $building->building_type_label;//arabic translation
        return response()->json($building->persons);
    }

    public function getBuildingInfo($id)
    {
        $building = Building::with(['families', 'families.persons'])->findOrFail($id);

        return response()->json([
            'error' => false,
            'data' => $building
        ]);
    }

    public function personDetail($id)
    {
        $person = Person::findOrFail($id);
        return view('plugins/building::person-detail', compact('person'));
    }

    public function editFamily($id)
    {
        $family = Family::findOrFail($id);
        return view('plugins/building::family-details', compact('family'));
    }

    public function addFamily($id)
    {
        $building = Building::findOrFail($id);
        return view('plugins/building::family-add', compact('building'));
    }

    public function updateFamily(Request $request, $id)
    {
        // ✅ 1. Validate the request based on families table
        $validated = $request->validate([
            'family_name'          => 'required|string|max:255',
            'family_number'        => 'required|string|max:120',
            'floor_number'         => 'nullable|integer',
            'apartment_id'         => 'nullable|integer',
            'family_code'          => 'nullable|in:A,B,C,D,E',
            'address'              => 'nullable|string|max:255',
            'region_id'            => 'nullable|integer',
            'phone'                => 'nullable|string|max:20',
            'notes'                => 'nullable|string',
            'status'               => 'required|in:active,inactive',
            'count_family_members' => 'nullable|integer|min:0',
            'building_id'          => 'nullable|integer',
            'house_type'           => 'nullable|in:ملك,إيجار,غير ذلك',
        ]);

        // ✅ 2. Find the family
        $family = Family::findOrFail($id);

        // ✅ 3. Update family fields
        $family->update($validated);

        return $this
            ->httpResponse()
            ->setMessage(trans('تم تحديث المعلومات بنجاح'));
    }

    public function deleteFamily($id)
    {
        $family = Family::findOrFail($id);
        $family->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف العائلة بنجاح',
        ]);
    }

}
