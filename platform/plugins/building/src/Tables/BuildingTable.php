<?php

namespace Botble\Building\Tables;

use Botble\Building\Models\Building;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\Column;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Botble\Base\Facades\Assets;
use Botble\Table\Columns\IdColumn;

class BuildingTable extends TableAbstract
{
    public function setup(): void
{
    $this
        ->model(Building::class)
        ->addHeaderAction(CreateHeaderAction::make()->route('building.create'))
        ->addActions([
            EditAction::make()->route('building.edit'),
            DeleteAction::make()->route('building.destroy'),
        ])
        ->addColumns([
            IdColumn::make(),
            Column::make('building_number')->title('رقم المبنى'), // سيظهر: رقم المبنى - اسم المنطقة
            Column::make('area.name')->title('القطاع'),
            Column::make('floors_count')->title('عدد الطوابق'),
        ])
        ->addBulkActions([
            DeleteBulkAction::make()->permission('building.destroy'),
        ])
        ->addBulkChanges([
            StatusBulkChange::make(),
            CreatedAtBulkChange::make(),
        ])
        ->queryUsing(function (Builder $query) {
            $query
                ->leftJoin('areas', 'areas.id', '=', 'buildings.area_id')
                ->select([
                    'buildings.id',
                    'buildings.area_id',
                    DB::raw("CONCAT(buildings.building_number, ' ', COALESCE(areas.name, '')) AS building_number"),
                    'buildings.floors_count',
                    'buildings.status',
                    'buildings.created_at',
                ])
                ->with('area'); // لعمود area.name
        });

        Assets::addStylesDirectly(asset('css/custom-datatables.css'));

}
}
