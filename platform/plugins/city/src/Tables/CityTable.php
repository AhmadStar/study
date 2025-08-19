<?php

namespace Botble\City\Tables;

use Botble\City\Models\City;
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

class CityTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(City::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('city.create'))
            ->addActions([
                EditAction::make()->route('city.edit'),
                DeleteAction::make()->route('city.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                Column::make('name')->title('اسم المدينة')->linkToEdit(),
                Column::make('country')->title('الدولة'),
                Column::make('population_estimate')->title('التعداد السكاني'),
                StatusColumn::make(),
                CreatedAtColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('city.destroy'),
            ])
            ->addBulkChanges([
                TextBulkChange::make()
                    ->name('name')
                    ->title('اسم المدينة'),
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query) {
                $query->select([
                    'id',
                    'name',
                    'country',
                    'population_estimate',
                    'status',
                    'created_at',
                ]);
            });
    }
}
