<?php

namespace Giftbox\ApplicationCore\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model {
    protected $table = 'theme';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function coffretsTypes()
    {
        return $this->hasMany(Coffret_type::class, 'theme_id');
    }
}
