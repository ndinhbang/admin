<?php

namespace App\Libs\Rememberable\Query;

use DateTime;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

class Builder extends QueryBuilder
{
    /**
     * The key that should be used when caching the query.
     *
     * @var string
     */
    protected $cacheKey;

    /**
     * The number of seconds to cache the query.
     *
     * @var int
     */
    protected $cacheSeconds;

    /**
     * The tags for the query cache.
     *
     * @var array
     */
    protected $cacheTags = [];

    /**
     * The cache driver to be used.
     *
     * @var string
     */
    protected $cacheDriver;

    /**
     * A cache prefix.
     *
     * @var string
     */
    protected $cachePrefix = 'rememberable';

    /**
     * Indicate that the query results should be serialized to json before saved
     *
     * @var boolean
     */
    protected $jsonSerialized = true;

    /**
     * {@inheritdoc}
     *
     * @param  array $columns
     * @return mixed
     */
    public function get($columns = ['*'])
    {
        if (config('rememberable.enabled', true) === false || is_null($this->cacheSeconds)) {
            return parent::get($columns);
        }
        if (!$this->jsonSerialized) {
            return $this->getCached($columns);
        }
        if (is_array( $result = json_decode($this->getCached($columns), true))) {
            return new Collection($result);
        }

        return $result;
    }

    /**
     * Execute the query as a cached "select" statement.
     *
     * @param  array $columns
     * @return array|string
     */
    public function getCached($columns = ['*'])
    {
        if (is_null($this->columns)) {
            $this->columns = $columns;
        }

        // If the query is requested to be cached, we will cache it using a unique key
        // for this database connection and query statement, including the bindings
        // that are used on this query, providing great convenience when caching.
        list($key, $seconds) = $this->getCacheInfo();

        $cache = $this->getCache();

        $callback = $this->getCacheCallback($columns);

        if (config('rememberable.query_log', false) === true) {
            $this->queryLogging();
        }

        // If we've been given a DateTime instance or a "seconds" value that is
        // greater than zero then we'll pass it on to the remember method.
        // Otherwise we'll cache it indefinitely.
        if ($seconds instanceof DateTime || $seconds > 0) {
            return $cache->remember($key, $seconds, $callback);
        }

        return $cache->rememberForever($key, $callback);
    }

    /**
     * Get the cache key and cache seconds as an array.
     *
     * @return array
     */
    protected function getCacheInfo()
    {
        return [$this->getCacheKey(), $this->cacheSeconds];
    }

    /**
     * Get a unique cache key for the complete query.
     *
     * @return string
     */
    public function getCacheKey()
    {
        return $this->cachePrefix . ':' . ($this->cacheKey ?: $this->generateCacheKey());
    }

    /**
     * Generate the unique cache key for the query.
     *
     * @return string
     */
    public function generateCacheKey()
    {
        $name = $this->connection->getName();
        return hash('sha1', $name . $this->toSql() . serialize($this->getBindings()));
    }

    /**
     * Get the cache object with tags assigned, if applicable.
     *
     * @return \Illuminate\Cache\CacheManager
     */
    protected function getCache()
    {
        $cache = $this->getCacheDriver();

        return $this->cacheTags ? $cache->tags($this->cacheTags) : $cache;
    }

    /**
     * Get the cache driver.
     *
     * @return \Illuminate\Cache\CacheManager
     */
    protected function getCacheDriver()
    {
        return app('cache')->driver($this->cacheDriver);
    }

    /**
     * Get the Closure callback used when caching queries.
     *
     * @param  array $columns
     * @return \Closure
     */
    protected function getCacheCallback($columns)
    {
        return function () use ($columns) {
            if ($this->jsonSerialized) {
                return parent::get($columns)->toJson(true);
            }
            return parent::get($columns);
        };
    }

    /**
     * Indicate that the query results should be cached forever.
     *
     * @param  string $key
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function rememberForever($key = null)
    {
        return $this->remember(-1, $key);
    }

    /**
     * Indicate that the query results should be cached.
     *
     * @param  \DateTime|int $seconds
     * @param  string        $key
     * @return $this
     */
    public function remember($seconds, $key = null)
    {
        list($this->cacheSeconds, $this->cacheKey) = [$seconds, $key];

        return $this;
    }

    /**
     * Indicate that the query should not be cached. Alias for dontRemember().
     *
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function doNotRemember()
    {
        return $this->dontRemember();
    }

    /**
     * Indicate that the query should not be cached.
     *
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function dontRemember()
    {
        $this->cacheSeconds = $this->cacheKey = $this->cacheTags = null;

        return $this;
    }

    /**
     * Indicate that the results, if cached, should use the given cache tags.
     *
     * @param  array|mixed $cacheTags
     * @return $this
     */
    public function cacheTags($cacheTags)
    {
        $this->cacheTags = (array)$cacheTags;

        return $this;
    }

    /**
     * Indicate that the results, if cached, should use the given cache driver.
     *
     * @param  string $cacheDriver
     * @return $this
     */
    public function cacheDriver($cacheDriver)
    {
        $this->cacheDriver = $cacheDriver;

        return $this;
    }

    /**
     * Flush the cache for the current model or a given tag name
     *
     * @param  mixed $cacheTags
     * @return boolean
     */
    public function flushCache($cacheTags = null)
    {
        $cache = $this->getCacheDriver();

        if (!method_exists($cache, 'tags')) {
            return false;
        }

        $cacheTags = $cacheTags ?: $this->cacheTags;

        $cache->tags($cacheTags)->flush();

        return true;
    }

    /**
     * Set the cache prefix.
     *
     * @param string $prefix
     * @return $this
     */
    public function prefix($prefix)
    {
        $this->cachePrefix = $prefix;

        return $this;
    }

    /**
     * Indicate that the results, before cached, should transform to json
     *
     * @param bool $jsonSerialized
     * @return Builder
     */
    public function shouldJsonSerialized($jsonSerialized = true)
    {
        $this->jsonSerialized = (boolean) $jsonSerialized;

        return $this;
    }

    /**
     * Add cache tags
     *
     * @param array $tags
     * @return $this
     */
    public function addCacheTags(array $tags = [])
    {
        $this->cacheTags = array_replace($this->cacheTags, $tags);

        return $this;
    }

    /**
     * Return full sql after binding parameters
     *
     * @return string
     */
    protected function toRawSql()
    {
        $query = $this->toSql();
        $pdo = $this->connection->getPdo();
        $bindings = $this->connection->prepareBindings( $this->getBindings() );

        if (!empty($bindings)) {
            foreach ($bindings as $key => $binding) {
                // This regex matches placeholders only, not the question marks,
                // nested in quotes, while we iterate through the bindings
                // and substitute placeholders by suitable values.
                $regex = is_numeric($key)
                    ? "/\?(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/"
                    : "/:{$key}(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/";

                // Mimic bindValue and only quote non-integer and non-float data types
                if (!is_int($binding) && !is_float($binding)) {
                    $binding = $pdo->quote($binding);
                }

                $query = preg_replace($regex, $binding, $query, 1);
            }
        }
        // Removes extra spaces at the beginning and end of the SQL query and its lines.
        $query = trim(preg_replace("/\s*\n\s*/", "\n", $query));

        return $query;
    }

    /**
     * Log query and its equivalent cache key to Debugbar message
     * You should install debugbar
     *
     * @return void
     */
    protected function queryLogging()
    {
        if (app()->offsetExists('debugbar')) {
            app('debugbar')->addMessage($this->toRawSql(), $this->getCacheKey());
        }
    }
}
