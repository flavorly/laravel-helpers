<?php

namespace Flavorly\LaravelHelpers\Helpers\Locker;

use Closure;
use Exception;
use Illuminate\Cache\CacheManager;
use Illuminate\Cache\RedisStore;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use ReflectionClass;

class Locker
{
    /**
     * @throws Exception
     */
    public function __construct(
        protected Model $model,
        protected bool $executedIfAlreadyLocked = false,
        protected int $lockFor = 10,
        protected int $waitForLock = 3,
        protected ?string $owner = null,
        protected ?string $id = null,
        protected string $idPrefix = '',
    ) {
        if (config('cache.default') !== 'redis') {
            throw new Exception('Cache driver must be redis');
        }

        if ($id == null) {
            $this->id = $this->getIdFromModel();
        }
    }

    /**
     * Get the ID of the lock
     */
    public function getId(): string
    {
        $id = $this->id ?? $this->getIdFromModel();

        return sprintf('%s%s', $this->idPrefix, $id);
    }

    /**
     * Get the ID of the lock.
     */
    public function getIdFromModel(): string
    {
        // locks_App\Models\User_1
        $key = $this->model->getKeyName();

        return Str::snake(sprintf(
            '%s_%s_%s_%s',
            'locks',
            strtolower($this->getModelName()),
            $this->model->{$key},
            $this->model?->created_at?->timestamp ?? '-',
        ));
    }

    /**
     * Set the ID of the lock to the given value
     *
     * @return $this
     */
    public function id(string $id): Locker
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set a prefix to the id, useful for multi tenancy or testing
     *
     * @return $this
     */
    public function prefix(string $prefix): Locker
    {
        $this->idPrefix = $prefix;

        return $this;
    }

    /**
     * If the resource is already locked skip and execute the callback
     */
    public function allowIfAlreadyLocked(): static
    {
        $this->executedIfAlreadyLocked = true;

        return $this;
    }

    /**
     * If the resource is already locked skip and don't execute the callback
     */
    public function dontAllowIfAlreadyLocked(): static
    {
        $this->executedIfAlreadyLocked = false;

        return $this;
    }

    /**
     * Get the actual Lock Owner
     */
    public function getOwner(): ?string
    {
        return $this->owner;
    }

    /**
     * Get the prefix
     */
    public function getPrefix(): string
    {
        return $this->idPrefix;
    }

    /**
     * Locks a resource, if a closure is given the lock is released after the closure is executed
     */
    public function lock(?Closure $closure = null): bool|string
    {
        if ($this->isLocked() && $this->executedIfAlreadyLocked) {
            if ($closure) {
                $closure();
            }

            return true;
        }

        $lock = Cache::lock($this->getId(), $this->lockFor);

        try {
            if ($lock->block($this->waitForLock, $closure)) {
                $this->owner = $lock->owner();

                return (string) $this->owner;
            }
        } catch (LockTimeoutException $exception) {
            if ($this->executedIfAlreadyLocked && $closure) {
                $closure();

                return true;
            }
        }

        return false;
    }

    /**
     * Attempts to Lock a resource, same as lock, but if lock is not acquired it throws an exception
     *
     * @throws LockTimeoutException
     */
    public function lockOrThrow(?Closure $closure = null, mixed $resourceId = null, ?string $resourceName = null): bool
    {
        if (! $this->lock($closure)) {
            throw new LockTimeoutException(
                trans('generic.resource-locked',
                    [
                        'id' => $resourceId ?? $this?->model->id ?? '-',
                        'subject' => $resourceName ?? $this->getModelName(),

                    ]
                )
            );
        }

        return true;
    }

    /**
     * Lock the resource for a specific duration
     *
     * @return $this
     */
    public function duration(int $duration = 10): static
    {
        $this->lockFor = $duration;

        return $this;
    }

    /**
     * How much time to wait if lock is in place
     *
     * @return $this
     */
    public function waitFor(int $seconds = 3): static
    {
        $this->waitForLock = $seconds;

        return $this;
    }

    /**
     * Release the lock
     */
    public function release(): bool
    {
        Cache::lock($this->getId())->forceRelease();
        $this->owner = null;

        return true;
    }

    /**
     * Check if the resource is locked
     */
    public function isLocked(): bool
    {
        /** @var RedisStore $store */
        $store = Cache::store('redis');
        /** @var CacheManager $lockConnection */
        $lockConnection = $store->lockConnection();

        return $lockConnection->get(Cache::getPrefix().$this->getId()) !== null;
    }

    /**
     * Return the restore Lock
     */
    public function restore(): Lock
    {
        return Cache::restoreLock($this->getId(), (string) $this->owner);
    }

    /**
     * Clear all locks for the current model
     */
    public function clearAll(): void
    {
        /** @var RedisStore $store */
        $store = Cache::store('redis');
        /** @var \Illuminate\Redis\Connections\Connection $redis */
        $redis = $store->lockConnection();

        // Get all locks for this model - match the correct pattern
        $pattern = sprintf('%slocks_%s*', Cache::getPrefix(), strtolower($this->getModelName()));
        $keys = $redis->keys($pattern);

        if (! empty($keys)) {
            foreach ($keys as $key) {
                $lock_key = str_replace([Cache::getPrefix(), 'database_'], '', $key);
                Cache::lock($lock_key)->forceRelease();
            }
        }
    }

    /**
     * Get the model name formatted.
     */
    protected function getModelName(): string
    {
        try {
            return (new ReflectionClass($this->model))->getShortName();
        } catch (Exception $e) {
            $result = strrchr(__CLASS__, '\\');

            return substr($result !== false ? $result : '', 1);
        }
    }

    /**
     * Get all active locks in the system
     *
     * @return array<string, mixed> Array of lock information
     */
    public static function getAllLocks(): array
    {
        /** @var RedisStore $store */
        $store = Cache::store('redis');
        /** @var \Illuminate\Redis\Connections\Connection $redis */
        $redis = $store->lockConnection();

        // Get all locks with pattern locks_*
        $pattern = sprintf('%s%s_*', Cache::getPrefix(), 'locks');
        $keys = $redis->keys($pattern);

        $locks = [];
        foreach ($keys as $key) {
            $locks[$key] = [
                'owner' => $redis->get($key),
                'ttl' => $redis->ttl($key),
            ];
        }

        return $locks;
    }
}
