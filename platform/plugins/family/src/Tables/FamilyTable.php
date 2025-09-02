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
use Botble\Base\Facades\Assets;

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
                Column::make('head_name')->title('اسم رب الأسرة'),
                Column::make('family_code')->title('كود العائلة'),
                Column::make('smember_name')->title('اسم عنصر الدراسات'),
                Column::make('building.name')->title('البناء'),
                Column::make('count_family_members')->title('عدد افراد العائلة'),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('family.destroy'),
            ])
            ->addBulkChanges([
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query) {
                $query->select(['id','family_code','smember_name','building_id','head_name','count_family_members'])->with('building');
            });

            Assets::addStylesDirectly("
                <style>
                    table.table thead th { text-align: right !important; }
                </style>
            ");

    }
}


