<?php

declare(strict_types=1);

namespace App\Exceptions\ReadModel;

use LogicException;

final class CannotPerformWriteOperation extends LogicException
{
    public static function forReadModel($model): self
    {
        return new self(
            sprintf(
                'Write operations are prohibited on read model %s.',
                $model
            )
        );
    }
}
