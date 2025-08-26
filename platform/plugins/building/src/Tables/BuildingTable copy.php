<?php

namespace Botble\Building\Tables;

use Botble\Building\Models\Building;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\TextBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

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
                Column::make('building_number')->title('رقم المبنى')->linkToEdit(),
                Column::make('area_id')->title('المنطقة'),
                Column::make('floors_count')->title('عدد الطوابق'),
                Column::make('latitude')->title('خط العرض'),
                Column::make('longitude')->title('خط الطول'),
                StatusColumn::make(),
                CreatedAtColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('building.destroy'),
            ])
            ->addBulkChanges([
                TextBulkChange::make()->name('building_number')->title('رقم المبنى'),
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query) {
                $query->select(['id','area_id','building_number','floors_count','latitude','longitude','status','created_at']);
            });
    }
}
