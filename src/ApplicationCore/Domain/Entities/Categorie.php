<?php

namespace Giftbox\ApplicationCore\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Giftbox\ApplicationCore\Domain\Entities\Prestation;

class Categorie extends Model {
    protected $table = 'categorie';
    protected $primaryKey = 'id';
    public $timestamps = false;


  public function prestations() {
      return $this->hasMany(Prestation::class, 'cat_id', 'id');
  }
}