## Laravel Helpers (flavorly/laravel-helpers)

A collection of reusable Laravel helpers for models, enums, math, casts, macros, and more.

### Key Features

- **EnumConcern trait**: Add `equals()`, `is()`, `getLabel()`, `toOptions()`, `toValues()`, `tryFromLabel()`, and more to backed enums.
- **Math helper**: Precise decimal arithmetic via `Math::of()` or the `math()` global. Supports sum, subtract, multiply, divide, percentages, comparisons, and configurable rounding/scale.
- **Model traits**: `HasSlug` (auto-generates slugs from attributes), `HasUUID` (auto-generates UUIDs on creation), `HasQueryTimeScopes` (time-based query scopes like `whereInLastDays()`, `whereInLastMonths()`), `HasBooleanStates` (success/failure tracking).
- **Casts**: `CollectionOfData` (cast JSON to a collection of Spatie Data objects), `FloatScaleCast` (decimal precision), `TrimString` (whitespace trimming).
- **Collection macros**: `paginate()`, `orderByIds()`, `toJsonResponse()`.
- **Global helpers**: `math()`, `tokenize()`, `get_morph_map_for()`, `data_get_fallback()`, `url_to_upload_file()`, `mock_fixture()`.
- **TypeScript transformers**: Custom `DataTypescriptTransformer` and `DataTypescriptCollector` extending Spatie's transformers with better BaseData support.

### Usage Patterns

When using enums, apply the `EnumConcern` trait on backed enums:

```php
enum Status: string
{
    use \Flavorly\LaravelHelpers\Concerns\EnumConcern;

    case Active = 'active';
    case Inactive = 'inactive';
}

$status->getLabel();          // translated label
Status::toOptions();          // Collection of OptionData for selects
$status->is(Status::Active);  // comparison
```

For precise math, use `Math::of()` or the `math()` helper:

```php
use Flavorly\LaravelHelpers\Helpers\Math\Math;

Math::of(100)->sum(50)->subtract(25)->value();     // 125
Math::of(200)->percentage(15)->value();             // 30
math(1000)->divide(3)->format();                    // "333.33"
```

For model slugs, implement `ImplementsSlug` and use the `HasSlug` trait:

```php
use Flavorly\LaravelHelpers\Concerns\HasSlug;
use Flavorly\LaravelHelpers\Contracts\ImplementsSlug;

class Post extends Model implements ImplementsSlug
{
    use HasSlug;

    public function getSlugAttributes(): array
    {
        return ['title'];
    }
}
```

For casting JSON columns to typed collections:

```php
protected function casts(): array
{
    return [
        'items' => CollectionOfData::using(ItemData::class),
    ];
}
```
