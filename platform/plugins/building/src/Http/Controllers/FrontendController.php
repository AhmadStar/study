<?php

namespace Botble\Building\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Building\Models\Building;
use Botble\Person\Models\Person;
use Botble\Family\Models\Family;
use Illuminate\Http\Request;

class FrontendController extends BaseController
{
    public function map(Request $request)
    {
        $buildings = Building::with('persons')->get();

        if($request->has('test'))
            return view('plugins/building::map-old', ['buildings' => $buildings])->render();
            else
        return view('plugins/building::map', ['buildings' => $buildings])->render();
    }
    public function getAllBuildings(Request $request)
    {
        // Validation
        $request->validate([
            'building_type'       => ['nullable'],           // string | array
            'is_empty'            => ['nullable', 'boolean'],
            'floors_count'        => ['nullable', 'integer'],
            'min_floors'          => ['nullable', 'integer'],
            'max_floors'          => ['nullable', 'integer'],
            // NEW:
            'family_head'         => ['nullable', 'string'],
            'only_with_families'  => ['nullable', 'boolean'],
        ]);

        $query = Building::query();

        // building_type filter
        if ($request->filled('building_type')) {
            $types = is_array($request->building_type)
                ? $request->building_type
                : array_map('trim', explode(',', (string) $request->building_type));
            $query->whereIn('building_type', $types);
        }

        // is_empty filter
        if ($request->has('is_empty') && $request->is_empty !== '') {
            $query->where('is_empty', $request->boolean('is_empty'));
        }

        // floors_count exact/range
        if ($request->filled('floors_count')) {
            $query->where('floors_count', (int) $request->floors_count);
        }
        if ($request->filled('min_floors')) {
            $query->where('floors_count', '>=', (int) $request->min_floors);
        }
        if ($request->filled('max_floors')) {
            $query->where('floors_count', '<=', (int) $request->max_floors);
        }

        // 🔎 NEW: filter buildings by related families.head_name
        if ($request->filled('family_head')) {
            $term = $request->input('family_head');
            $query->whereHas('families', function ($qq) use ($term) {
                $qq->where('head_name', 'like', '%' . $term . '%');
            });
        }

        // 🧩 NEW: only return buildings that have families
        if ($request->boolean('only_with_families')) {
            $query->has('families');
        }

        // keep payload small for the map
        $buildings = $query->get(['id', 'name', 'latitude', 'longitude']);

        return $buildings->map(function ($b) {
            return [
                'id'        => $b->id,
                'name'      => $b->name,
                'latitude'  => (float) $b->latitude,
                'longitude' => (float) $b->longitude,
            ];
        })->values()->toJson(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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
