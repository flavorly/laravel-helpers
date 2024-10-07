<?php

namespace Flavorly\LaravelHelpers\Helpers\LaravelData;

use ReflectionClass;
use Spatie\LaravelData\Contracts\BaseData;
use Spatie\LaravelData\Contracts\BaseData as BaseDataContract;
use Spatie\LaravelData\Support\TypeScriptTransformer\DataTypeScriptCollector as BaseDataTypeScriptCollector;
use Spatie\TypeScriptTransformer\Structures\TransformedType;

class DataTypescriptCollector extends BaseDataTypeScriptCollector
{
    public function getTransformedType(ReflectionClass $class): ?TransformedType
    {
        if (! $class->isSubclassOf(BaseData::class) && ! $class->implementsInterface(BaseDataContract::class)) {

            return null;
        }

        $transformer = new DataTypescriptTransformer($this->config);

        return $transformer->transform($class, $class->getShortName());
    }
}
