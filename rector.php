<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\BooleanNot\ReplaceMultipleBooleanNotRector;
use Rector\CodeQuality\Rector\ClassMethod\ExplicitReturnNullRector;
use Rector\CodeQuality\Rector\Empty_\SimplifyEmptyCheckOnEmptyArrayRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\If_\RemoveAlwaysTrueIfConditionRector;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\Set\ValueObject\SetList;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withSkip([
        // Skip vendor directory
        __DIR__.'/vendor',

        // Skip rules that conflict with Laravel/Spatie conventions
        StringClassNameToClassConstantRector::class,
        ExplicitReturnNullRector::class,
        SimplifyEmptyCheckOnEmptyArrayRector::class,

        // Skip strict empty() replacements - too aggressive for mixed types
        DisallowedEmptyRuleFixerRector::class,

        // Skip rules that can break logic with nullable types
        RemoveAlwaysTrueIfConditionRector::class,
        ReplaceMultipleBooleanNotRector::class,

        // Skip override attribute for Laravel models (conflicts with Eloquent magic)
        AddOverrideAttributeToOverriddenMethodsRector::class => [
            __DIR__.'/src/Models',
        ],
    ])
    ->withPhpSets(php85: true)
    ->withSets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
    ]);
