<?php

namespace Botble\Family\Repositories\Eloquent;

use Botble\Family\Repositories\Interfaces\FamilyInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Support\Facades\DB;
use Botble\Base\Enums\BaseStatusEnum;

class FamilyRepository extends RepositoriesAbstract implements FamilyInterface
{

    public function getFamilyFilter(array $params)
    {
        $params = array_merge([
            'select' =>[
                'families.id',
                'families.name',
                'families.head_name',
            ],
        ], $params);

        $this->model = $this->model->select($params['select'])
            ->where($params['condition']);

        if (!empty($params['order_by'])) {
            foreach ($params['order_by'] as $column => $direction) {
                $this->model->orderBy($column, $direction);
            }
        }

        $totalAmount = 0;

        if ($params['export']) {
            // Fetch all records without pagination
            $paginator = $this->model->get();

            // Calculate the sum of the product of quantities and selling_price for all records
            // $totalAmount = $paginator->sum(function ($record) {
            //     return $record->selling_price * $record->quantities;
            // });
        } else {
            // Retrieve all records

            // Calculate the sum of the product of quantities and selling_price for all records
            // $totalAmount = $records->sum(function ($record) {
            //     return $record->selling_price * $record->quantities;
            // });

            // Get the total count of all records without pagination
            $totalCount = $this->model->get();

            // Calculate the number of records to skip
            $skip = ($params['paginate']['current_page'] - 1) * $params['paginate']['per_page'];

            // Paginate the results based on the provided parameters
            $paginator = $this->model->skip($skip)->take($params['paginate']['per_page'])->get();

        }

        $data = [
            'data' => $paginator,
            'totalAmount' => $totalAmount,
            'total' => isset($totalCount) ? count($totalCount) : 0,
            'recordsTotal' => isset($totalCount) ? count($totalCount) : 0,
            'recordsFiltered' => isset($totalCount) ? count($totalCount) : 0,
        ];

        return $data;
    }

}
