<?php

namespace App\Traits;

trait MorphTrait
{
    public function getMorphedModel($model_id, $model_type)
    {
        return $model_type::query()->find($model_id);
    }

    public function getMorphedTable($model_type)
    {
        return (new $model_type)->getTable();
    }
}
