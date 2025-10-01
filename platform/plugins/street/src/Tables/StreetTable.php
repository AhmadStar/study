<?php

namespace Botble\Street\Tables;

use Botble\Street\Models\Street;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Botble\Table\Columns\Column;
use Botble\Base\Facades\Assets;

class StreetTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Street::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('street.create'))
            ->addActions([
                EditAction::make()->route('street.edit'),
                DeleteAction::make()->route('street.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                // NameColumn::make()->route('street.edit'),
                Column::make('street_name')->title('رقم الشارع'), // سيظهر: رقم المبنى - اسم المنطقة
                CreatedAtColumn::make(),
                StatusColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('street.destroy'),
            ])
            ->addBulkChanges([
                NameBulkChange::make(),
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query) {
                $query
                ->leftJoin('areas', 'areas.id', '=', 'streets.area_id')
                ->select([
                    'streets.id',
                    'streets.name',
                    'area_id',
                    DB::raw("CONCAT(areas.name, ' ', COALESCE(streets.name, '')) AS street_name"),
                    'streets.created_at',
                    'streets.status',
                ]);
            });

        Assets::addStylesDirectly(asset('css/custom-datatables.css'));

    }


}
