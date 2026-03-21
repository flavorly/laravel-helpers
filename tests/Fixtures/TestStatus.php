<?php

namespace Flavorly\LaravelHelpers\Tests\Fixtures;

use Flavorly\LaravelHelpers\Concerns\EnumConcern;

enum TestStatus: string
{
    use EnumConcern;

    case Active = 'active';
    case Inactive = 'inactive';
    case Pending = 'pending';

    public static function getLabels(): ?array
    {
        return [
            'active' => 'Currently Active',
            'inactive' => 'Not Active',
            'pending' => 'Awaiting Review',
        ];
    }
}
