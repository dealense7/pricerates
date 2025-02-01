<?php

declare(strict_types=1);

namespace App\CacheRepositories;

use Closure;
use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Support\Facades\Cache;

use function is_null;
use function is_scalar;
use function str_starts_with;
use function strtolower;
use function vsprintf;

abstract class CacheRepository
{
    protected const DELIMITER_KEY   = '#';
    protected const DELIMITER_SCOPE = ':';

    protected string $cacheKey = '';
    protected int $cacheTtl = 1800; // 30 minutes
    protected array $cacheTags = [];

    /**
     * @var \Illuminate\Contracts\Cache\Repository|null
     */
    protected ?CacheContract $cache = null;

    protected static ?int $globalTtl = null;

    public function remember(string $key, Closure $closure, ?int $minutes = null)
    {
        $cache = $this->getCache($this->getTags());

        $value = $cache->get($key);

        if (! is_null($value)) {
            return $value;
        }

        $cache->put($key, $value = $closure(), $minutes ? $minutes * 60 : $this->getTtl());

        return $value;
    }

    public function flush(?string $tag = null): void
    {
        $tag = $this->getCacheKey() . ($tag ? '_' . $tag : '');

        $cache = $this->getCache([$tag]);

        if ($this->supportsTags()) {
            $cache->flush();
        }
    }

    public function forget(?string $key = null): void
    {
        $this->getCache($this->getTags())->forget($key);
    }

    public function getTtl(): int
    {
        return self::$globalTtl ?? $this->cacheTtl;
    }

    public function setTtl(int $minutes): CacheRepository
    {
        $this->cacheTtl = $minutes;

        return $this;
    }

    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    public static function setGlobalTtl(int $ttl): void
    {
        self::$globalTtl = $ttl;
    }

    public static function resetGlobalTtl(): void
    {
        self::$globalTtl = null;
    }

    public static function hash($data): string
    {
        if (is_array($data)) {
            sort($data);
            $data = serialize($data);
        }

        if (is_object($data)) {
            $data = serialize($data);
        }

        return sha1((string) $data);
    }

    protected function getKey(string $key, ...$params): string
    {
        $delimiter = str_starts_with($key, '%') ? self::DELIMITER_KEY : self::DELIMITER_SCOPE;

        return $this->generateKey($key, $delimiter, $params);
    }

    protected function generateKey(string $key, $delimiter = self::DELIMITER_SCOPE, array $params = []): string
    {
        $fullKey = $this->getCacheKey() . $delimiter . $key;

        if (! empty($params)) {
            $fullKey = vsprintf($fullKey, $params);
        }

        return $fullKey;
    }

    protected function getTags(): array
    {
        return $this->cacheTags;
    }

    protected function setTags(array $tags): CacheRepository
    {
        $this->cacheTags = $tags;

        return $this;
    }

    protected function setTag(): CacheRepository
    {
        $this->setTags([$this->getCacheKey()]);

        return $this;
    }

    protected function clearByTag(): void
    {
        if ($this->supportsTags()) {
            Cache::tags([$this->getCacheKey()])->flush();
        }
    }

    protected function setCache(CacheContract $cache): CacheRepository
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @param  array  $tags
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function getCache(array $tags = []): CacheContract
    {
        if ($this->supportsTags() && ! empty($tags)) {
            return Cache::store()->tags($tags);
        }

        return Cache::store();
    }

    protected function createKeyFromArgs(array $args, ?string $prefix = null, ?string $postfix = null): string
    {
        $key = $this->getCacheKey();

        if (! empty($prefix)) {
            $key .= self::DELIMITER_SCOPE . strtolower($prefix);
        }

        foreach ($args as $argKey => $argValue) {
            $argValue = $this->parseArgValue($argValue);

            $key .= self::DELIMITER_SCOPE . $argKey . self::DELIMITER_KEY . $argValue;
        }

        if (! is_null($postfix)) {
            $key .= self::DELIMITER_SCOPE . $postfix;
        }

        return $key;
    }

    private function parseArgValue($argValue = 'null'): string
    {
        return is_scalar($argValue) ? (string) $argValue : $this->hash($argValue);
    }

    private function supportsTags(): bool
    {
        return Cache::getStore() instanceof TaggableStore;
    }
}
