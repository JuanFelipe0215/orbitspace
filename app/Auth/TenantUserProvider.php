<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Hashing\Hasher;

class TenantUserProvider extends EloquentUserProvider
{
    public function __construct(Hasher $hasher, string $model)
    {
        parent::__construct($hasher, $model);
    }

    protected function newModelQuery($model = null)
    {
        $model = is_null($model) ? $this->createModel() : $model;

        // Set connection BEFORE newQuery() so the QueryBuilder is built on the right DB.
        // When tenancy is active the bootstrapper has already switched the default to 'tenant'.
        if (tenancy()->initialized) {
            $model->setConnection('tenant');
        }

        return $model->newQuery();
    }
}
