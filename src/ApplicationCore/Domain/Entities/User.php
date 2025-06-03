<?php

namespace Giftbox\ApplicationCore\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Giftbox\ApplicationCore\Domain\Entities\Box;

class User extends Model {
    protected $table = 'user';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'password',
        'role'
    ];

    public function boxes()
    {
        return $this->hasMany(Box::class, 'createur_id', 'id');
    }
}
