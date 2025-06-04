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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                // Generate a unique ID
                $uniqueId = uniqid('', true);
                // Base64 encode it
                $base64Id = base64_encode($uniqueId);
                // Make it URL-safe (remove +, /, =)
                $safeBase64Id = str_replace(['+', '/'], ['-', '_'], rtrim($base64Id, '='));
                $model->{$model->getKeyName()} = $safeBase64Id;
            }
        });
    }

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
