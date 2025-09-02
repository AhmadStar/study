<?php

namespace Botble\Person\Tables;

use Botble\Person\Models\Person;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\TextBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\Column;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;
use Botble\Base\Facades\Assets;

class PersonTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Person::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('person.create'))
            ->addActions([
                EditAction::make()->route('person.edit'),
                DeleteAction::make()->route('person.destroy'),
            ])
            ->addColumns([
                Column::make('name')->title('الاسم')->linkToEdit(),
                Column::make('gender')->title('الجنس'),
                Column::make('date_of_birth')->title('تاريخ الميلاد'),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('person.destroy'),
            ])
            ->addBulkChanges([
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query) {
                $query->select(['id','name','gender','date_of_birth']);
            });

            Assets::addStylesDirectly("
                <style>
                    table.table thead th { text-align: right !important; }
                </style>
            ");
    }
}
