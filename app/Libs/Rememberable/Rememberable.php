<?php

namespace App\Libs\Rememberable;

use App\Libs\Rememberable\Query\Builder;

trait Rememberable
{
    /**
     * {@inheritdoc}
     *
     * @return \App\Libs\Rememberable\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        $builder = new Builder($connection, $connection->getQueryGrammar(), $connection->getPostProcessor());

        if (isset($this->rememberFor)) {
            $builder->remember($this->rememberFor);
        }

        if (isset($this->rememberCacheTag)) {
            $builder->cacheTags($this->rememberCacheTag);
        }

        if (isset($this->rememberCachePrefix)) {
            $builder->prefix($this->rememberCachePrefix);
        }

        if (isset($this->rememberCacheDriver)) {
            $builder->cacheDriver($this->rememberCacheDriver);
        }

        if (isset($this->rememberJsonSerialized)) {
            $builder->shouldJsonSerialized($this->rememberJsonSerialized);
        }

        return $builder;
    }
}
