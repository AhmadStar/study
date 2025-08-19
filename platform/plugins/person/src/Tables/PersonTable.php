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
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

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
                IdColumn::make(),
                Column::make('first_name')->title('الاسم الأول')->linkToEdit(),
                Column::make('last_name')->title('اسم العائلة'),
                Column::make('gender')->title('الجنس'),
                Column::make('date_of_birth')->title('تاريخ الميلاد'),
                Column::make('relationship')->title('العلاقة بالعائلة'),
                Column::make('occupation')->title('الوظيفة'),
                StatusColumn::make(),
                CreatedAtColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('person.destroy'),
            ])
            ->addBulkChanges([
                TextBulkChange::make()->name('first_name')->title('الاسم الأول'),
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query) {
                $query->select(['id','first_name','last_name','gender','date_of_birth','relationship','occupation','status','created_at']);
            });
    }
}
