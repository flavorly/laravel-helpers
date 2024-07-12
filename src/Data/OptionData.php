<?php

namespace Flavorly\LaravelHelpers\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class OptionData extends Data
{
    public function __construct(
        public string $label,
        public mixed $value,
        public Optional|bool $disabled = false,
        public Optional|null|string $icon = null,
        public Optional|null|string $image = null,
        public Optional|null|string $groupLabel = null,
        public Optional|null|string $groupIcon = null,
        /** @var Collection<int,OptionData>|null */
        public Optional|Collection|null $items = null,
    ) {}
}
