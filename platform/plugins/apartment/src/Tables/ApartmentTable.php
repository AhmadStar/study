<?php

namespace Botble\Apartment\Tables;

use Botble\Apartment\Models\Apartment;
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

class ApartmentTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Apartment::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('apartment.create'))
            ->addActions([
                EditAction::make()->route('apartment.edit'),
                DeleteAction::make()->route('apartment.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                Column::make('apartment_number')->title('رقم الشقة')->linkToEdit(),
                Column::make('building_id')->title('المبنى'),
                Column::make('floor_number')->title('الطابق'),
                StatusColumn::make(),
                CreatedAtColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('apartment.destroy'),
            ])
            ->addBulkChanges([
                TextBulkChange::make()->name('apartment_number')->title('رقم الشقة'),
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query) {
                $query->select(['id','building_id','apartment_number','floor_number','status','created_at']);
            });
    }
}
