<?php

namespace Sync\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'name',
        'kommo_token',
        'unisender_api'
    ];

    /**
     * Получаем baseDomain
     * @return string
     */
    public function getDomainAttribute(): string
    {
        return (json_decode($this->token))->baseDomain;
    }

    public function scopeHasExpired(Builder $builder, $hours): Collection
    {
        return Account::all()->filter(function ($account) use ($hours) {
            $token = json_decode($account->kommo_token, true);
            return $account->where(
                'expires',
                '<=',
                (new Carbon($token['expires'] - time()))->hour <= (int)$hours);
        });
    }
}
