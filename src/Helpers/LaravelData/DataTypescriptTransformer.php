<?php

namespace Flavorly\LaravelHelpers\Helpers\LaravelData;

use ReflectionClass;
use ReflectionProperty;
use Spatie\LaravelData\Concerns\BaseData;
use Spatie\LaravelData\Contracts\BaseData as BaseDataContract;
use Spatie\LaravelData\Support\DataConfig;
use Spatie\LaravelData\Support\Lazy\ClosureLazy;
use Spatie\LaravelData\Support\TypeScriptTransformer\DataTypeScriptTransformer as BaseDataTypeScriptTransformer;
use Spatie\LaravelData\Support\TypeScriptTransformer\RemoveLazyTypeProcessor;
use Spatie\LaravelData\Support\TypeScriptTransformer\RemoveOptionalTypeProcessor;
use Spatie\TypeScriptTransformer\Attributes\Hidden;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypeScriptOptional;
use Spatie\TypeScriptTransformer\Structures\MissingSymbolsCollection;
use Spatie\TypeScriptTransformer\TypeProcessors\DtoCollectionTypeProcessor;
use Spatie\TypeScriptTransformer\TypeProcessors\ReplaceDefaultsTypeProcessor;

class DataTypescriptTransformer extends BaseDataTypeScriptTransformer
{
    public function canTransform(ReflectionClass $class): bool
    {
        $implementsBaseData = $class->implementsInterface(BaseDataContract::class);

        return $class->isSubclassOf(BaseData::class) || $implementsBaseData;
    }

    protected function typeProcessors(): array
    {
        return [
            new ReplaceDefaultsTypeProcessor(
                $this->config->getDefaultTypeReplacements()
            ),
            new RemoveLazyTypeProcessor,
            new RemoveOptionalTypeProcessor,
            new DtoCollectionTypeProcessor,
        ];
    }

    protected function transformProperties(
        ReflectionClass $class,
        MissingSymbolsCollection $missingSymbols
    ): string {

        $dataClass = app(DataConfig::class)->getDataClass($class->getName());

        $isOptional = $dataClass->attributes->contains(
            fn (object $attribute) => $attribute instanceof TypeScriptOptional
        );

        $nullablesAreOptional = $this->config->shouldConsiderNullAsOptional();

        return array_reduce(
            $this->resolveProperties($class),
            function (string $carry, ReflectionProperty $property) use ($isOptional, $dataClass, $missingSymbols, $nullablesAreOptional) {
                /** @var \Spatie\LaravelData\Support\DataProperty $dataProperty */
                $dataProperty = $dataClass->properties[$property->getName()];
                $type = $this->resolveTypeForProperty($property, $dataProperty, $missingSymbols);
                if ($type === null) {
                    return $carry;
                }

                $isHidden = ! empty($property->getAttributes(Hidden::class));

                if ($isHidden) {
                    return $carry;
                }

                $isOptional = $isOptional
                    || $dataProperty->attributes->contains(
                        fn (object $attribute) => $attribute instanceof TypeScriptOptional
                    )
                    || ($dataProperty->type->lazyType && $dataProperty->type->lazyType !== ClosureLazy::class)
                    || $dataProperty->type->isOptional
                    || ($property->getType()?->allowsNull() && ! $dataProperty->type->isMixed && $nullablesAreOptional);

                $transformed = $this->typeToTypeScript(
                    type: $type,
                    missingSymbolsCollection: $missingSymbols,
                    nullablesAreOptional: $nullablesAreOptional,
                    currentClass: $property->getDeclaringClass()->getName(),
                );

                $propertyName = $dataProperty->outputMappedName ?? $dataProperty->name;

                if (! preg_match('/^[$_a-zA-Z][$_a-zA-Z0-9]*$/', $propertyName)) {
                    $propertyName = "'{$propertyName}'";
                }

                return $isOptional
                    ? "{$carry}{$propertyName}?: {$transformed};".PHP_EOL
                    : "{$carry}{$propertyName}: {$transformed};".PHP_EOL;
            },
            ''
        );
    }
}
