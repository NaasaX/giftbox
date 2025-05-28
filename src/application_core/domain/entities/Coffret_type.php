<?php

namespace gift\application_core\domain\entities;

use Illuminate\Database\Eloquent\Model;

class Coffret_type extends Model {
    protected $table = 'coffret_type';
    protected $primaryKey = 'id';
    public $timestamps = false;


  public function prestations()
    {
        return $this->belongsToMany(Prestation::class, 'coffret2presta', 'coffret_id', 'presta_id');
    }
}