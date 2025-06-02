<?php

namespace Giftbox\ApplicationCore\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Giftbox\ApplicationCore\Domain\Entities\Prestation;

class Coffret_type extends Model {
    protected $table = 'coffret_type';
    protected $primaryKey = 'id';
    public $timestamps = false;


  public function prestations()
    {
        return $this->belongsToMany(Prestation::class, 'coffret2presta', 'coffret_id', 'presta_id');
    }
}