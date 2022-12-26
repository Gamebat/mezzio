<?php

namespace Sync\models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    /**
     * @var mixed
     */
    public mixed $token;
    /**
     * @var mixed
     */
    public mixed $name;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $fillable = [
        'name',
        'token'
    ];

    /**
     * Получение baseDomain
     * @return mixed
     */
    public function getDomainAttribute()
    {
        return (json_decode($this->token))->baseDomain;
    }
}