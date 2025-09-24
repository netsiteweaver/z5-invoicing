<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class CustomUserProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        // If identifier is null (remember me token lookup), search by remember_token only
        if ($identifier === null) {
            return $this->createModel()->newQuery()
                ->where('remember_token', $token)
                ->where('status', 1) // Only allow active users
                ->first();
        }

        // Otherwise, use the parent implementation
        return parent::retrieveByToken($identifier, $token);
    }
}
