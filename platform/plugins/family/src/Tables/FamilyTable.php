<?php

namespace Botble\Family\Tables;

use Botble\Family\Models\Family;
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

class FamilyTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Family::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('family.create'))
            ->addActions([
                EditAction::make()->route('family.edit'),
                DeleteAction::make()->route('family.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                Column::make('family_code')->title('كود العائلة')->linkToEdit(),
                Column::make('apartment_id')->title('الشقة'),
                Column::make('notes')->title('ملاحظات'),
                StatusColumn::make(),
                CreatedAtColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('family.destroy'),
            ])
            ->addBulkChanges([
                TextBulkChange::make()->name('family_code')->title('كود العائلة'),
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query) {
                $query->select(['id','apartment_id','family_code','notes','status','created_at']);
            });
    }
}
