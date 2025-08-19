<?php

namespace Botble\Building\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Building\Models\Building;
use Botble\Person\Models\Person;

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
        return response()->json($building->persons);
    }

    public function personDetail($id)
    {
        $person = Person::findOrFail($id);
        return view('plugins/building::person-detail', compact('person'));
    }
}
