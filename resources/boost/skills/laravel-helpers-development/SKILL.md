---
name: laravel-helpers-development
description: Build and work with flavorly/laravel-helpers features including Math operations, EnumConcern, model traits, custom casts, and collection macros.
---

# Laravel Helpers Development

## When to use this skill

Use this skill when working with `flavorly/laravel-helpers` features: precise math operations, enum helpers, model traits (slugs, UUIDs, time scopes), custom Eloquent casts, collection/string macros, or TypeScript transformers.

## Math Helper

The `Math` class provides precise decimal arithmetic using `brick/math` under the hood. Configuration is in `config/laravel-helpers.php` under the `math` key.

### Creating instances

```php
use Flavorly\LaravelHelpers\Helpers\Math\Math;

// Static factory (reads scale/rounding from config)
$math = Math::of(100);
$math = Math::of('99.99', scale: 4);

// Global helper
$math = math(100);
```

### Operations (all chainable, return Math)

```php
$math->sum(50);                    // add
$math->subtract(25);              // subtract
$math->multiply(2);               // multiply
$math->divide(3);                 // divide
$math->pow(2);                    // exponent
$math->percentage(15);            // calculate percentage
$math->addPercentage(21);         // add VAT/tax
$math->subtractPercentage(10);    // remove discount
```

### Comparisons (return bool)

```php
$math->isLessThan(200);
$math->isGreaterThan(50);
$math->isEqual(100);
$math->isLessThanOrEqual(100);
$math->isGreaterThanOrEqual(100);
$math->isPositive();
$math->isNegative();
$math->isZero();
```

### Output

```php
$math->value();          // float
$math->toString();       // string
$math->toInt();          // int
$math->toFloat();        // float
$math->format();         // formatted string with thousand separator
$math->toStorageScale(); // scaled int for database storage
```

## EnumConcern Trait

Apply to any backed enum for rich functionality:

```php
enum PaymentStatus: string
{
    use \Flavorly\LaravelHelpers\Concerns\EnumConcern;

    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';

    // Optional: override labels
    public static function getLabels(): ?array
    {
        return [
            'pending' => 'Awaiting Payment',
            'paid' => 'Payment Complete',
            'failed' => 'Payment Failed',
        ];
    }

    // Optional: translation namespace prefix for package enums
    public function packagePrefix(): string
    {
        return 'my-package::';
    }
}
```

### Available methods

- `$enum->equals(Enum::Case)` / `$enum->is(Enum::Case)` — compare values
- `$enum->notEquals(Enum::Case)` / `$enum->isNot(Enum::Case)` — negative compare
- `$enum->getLabel()` — translated label (uses `getLabels()` or trans())
- `Enum::toOptions(?callable $map, ?callable $filter)` — Collection of `OptionData` for selects/dropdowns
- `Enum::toArray()` — array of labels
- `Enum::toValues()` — array of raw values
- `Enum::toCollection()` — Collection of labels
- `Enum::tryFromLabel('label')` — reverse-lookup by label
- `Enum::contains(Enum::Case)` — check if case exists
- `Enum::random()` — random case
- `Enum::firstWhere('value')` — find by value

## Model Traits

### HasSlug

Auto-generates URL slugs. Requires implementing `ImplementsSlug` contract:

```php
use Flavorly\LaravelHelpers\Concerns\HasSlug;
use Flavorly\LaravelHelpers\Contracts\ImplementsSlug;

class Article extends Model implements ImplementsSlug
{
    use HasSlug;

    public function getSlugAttributes(): array
    {
        return ['title'];  // supports relations: 'relation.attribute'
    }
}
```

### HasUUID

Auto-generates UUIDs on model creation:

```php
use Flavorly\LaravelHelpers\Concerns\HasUUID;

class Order extends Model
{
    use HasUUID;
    // Expects a `uuid` column on the table
}
```

### HasQueryTimeScopes

Time-based query scopes for Eloquent builders:

```php
use Flavorly\LaravelHelpers\Concerns\HasQueryTimeScopes;

// In a query builder or scope:
$query->whereInTimeFrame($start, $end);
$query->whereInLastMonths(3);
$query->whereInLastDays(7);
$query->whereInLastWeek();
$query->whereLastHours(12);
$query->whereIsNewerThanDays(30);
$query->whereIsOlderThanDays(90);
```

All time scope methods accept an optional `$column` parameter (defaults to `created_at`).

### HasBooleanStates

Track success/failure states on models.

## Custom Casts

### CollectionOfData

Cast a JSON column to a typed collection of Spatie Data objects:

```php
protected function casts(): array
{
    return [
        'line_items' => CollectionOfData::using(LineItemData::class),
        'tags' => CollectionOfData::using(TagData::class, TagCollection::class),
    ];
}
```

### FloatScaleCast

Configurable decimal precision for float columns.

### TrimString

Automatically trims whitespace from string attributes.

## Global Helper Functions

- `math($number)` — create a Math instance with app config
- `tokenize(...$args)` — join args with `:` separator
- `get_morph_map_for($class)` — resolve morph map alias
- `data_get_fallback($target, $keys, $default)` — dot-notation access with multiple fallback keys
- `url_to_upload_file($url)` — download URL to UploadedFile
- `mock_fixture($name)` — create Saloon test fixture

## Collection Macros

- `$collection->paginate($perPage, $page)` — paginate a collection
- `$collection->orderByIds($ids, $key)` — order by specific ID sequence
- `$collection->toJsonResponse()` — convert to JSON response

## TypeScript Integration

Custom transformers for better Spatie Data → TypeScript generation:

- `DataTypescriptTransformer` — handles BaseData implementations, respects `#[Hidden]` and `#[Optional]` attributes, supports output mapped names
- `DataTypescriptCollector` — collects all BaseData implementations for transformation

Configure in `config/laravel-helpers.php` under the `typescript` key.
