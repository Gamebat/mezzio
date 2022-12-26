<?php

namespace Sync\models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'name',
        'token'
    ];

    /**
     * Получаем baseDomain
     * @return string
     */
    public function getDomainAttribute(): string
    {
        return (json_decode($this->token))->baseDomain;
    }
}