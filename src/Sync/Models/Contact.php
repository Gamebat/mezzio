<?php

namespace Sync\models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email'
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