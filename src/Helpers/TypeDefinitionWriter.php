<?php

namespace Flavorly\LaravelHelpers\Helpers;

use Illuminate\Support\Str;
use Spatie\TypeScriptTransformer\Structures\TypesCollection;
use Spatie\TypeScriptTransformer\Writers\TypeDefinitionWriter as BaseWriter;

class TypeDefinitionWriter extends BaseWriter
{
    public function format(TypesCollection $collection): string
    {
        return Str::of(parent::format($collection))
            ->replaceMatches('/(\w+)\??: ([\w\.<>]+) \| null;/', function ($matches) {
                return sprintf('%s?: %s', $matches[1], $matches[2]);
            })
            ->replace(search: 'Flavorly.InertiaFlash.', replace: '')
            ->replace(search: 'Flavorly.LaravelHelpers.', replace: '')
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
