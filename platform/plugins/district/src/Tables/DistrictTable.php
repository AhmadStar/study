<?php

namespace Botble\District\Tables;

use Botble\District\Models\District;
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

class DistrictTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(District::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('district.create'))
            ->addActions([
                EditAction::make()->route('district.edit'),
                DeleteAction::make()->route('district.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                Column::make('name')->title('اسم الحي')->linkToEdit(),
                Column::make('city_id')->title('المدينة'),
                Column::make('population_estimate')->title('التعداد السكاني'),
                StatusColumn::make(),
                CreatedAtColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('district.destroy'),
            ])
            ->addBulkChanges([
                TextBulkChange::make()->name('name')->title('اسم الحي'),
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query) {
                $query->select(['id','city_id','name','population_estimate','status','created_at']);
            });
    }
}
