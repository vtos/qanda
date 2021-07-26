<?php

declare(strict_types=1);

namespace App\Models\Read;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\ReadModel\CannotPerformWriteOperation;

abstract class ReadModel extends Model
{
    protected function performInsert(Builder $query)
    {
        throw CannotPerformWriteOperation::forReadModel(self::class);
    }

    protected function performUpdate(Builder $query)
    {
        throw CannotPerformWriteOperation::forReadModel(self::class);
    }

    protected function performDeleteOnModel()
    {
        throw CannotPerformWriteOperation::forReadModel(self::class);
    }
}
