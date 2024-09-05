<?php

namespace Flavorly\LaravelHelpers\Helpers;

use Illuminate\Support\Str;
use Spatie\TypeScriptTransformer\Structures\TypesCollection;
use Spatie\TypeScriptTransformer\Writers\TypeDefinitionWriter as BaseWriter;

class TypeDefinitionWriter extends BaseWriter
{
    public function format(TypesCollection $collection): string
    {
        $replacements = config('laravel-helpers.typescript.replace', []);

        return Str::of(parent::format($collection))
            ->replaceMatches('/(\w+)(\??:)\s*([\w\s\[\]:<>,{}\.]+)(\s*\|\s*null)?;/', function ($matches) {
                $name = $matches[1];
                $type = $matches[3];
                $isOptional = $matches[2] === '?:' || isset($matches[4]);

                return sprintf('%s%s %s', $name, $isOptional ? '?:' : ':', trim($type));
            })
            // @phpstan-ignore-next-line
            ->replace(search: array_keys($replacements), replace: array_values($replacements))
            ->replace(search: 'App.Data', replace: 'Data')
            ->replace(search: 'App.Enums', replace: 'Enums')
            ->replace(search: 'App.Http.Requests', replace: 'Requests')
            ->replace(search: 'App.Http.Responses', replace: 'Responses')
            ->replace(search: 'App.Http.Views', replace: 'Views')
            ->replace(search: 'Domain.', replace: '')
            ->replace(search: 'Modules.', replace: '')
            ->replace(search: '.Data', replace: '')
            ->replace(search: '.Http', replace: '')
            ->toString();
    }
}
