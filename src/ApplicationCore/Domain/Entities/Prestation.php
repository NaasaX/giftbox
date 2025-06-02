<?php
namespace Giftbox\ApplicationCore\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Giftbox\ApplicationCore\Domain\Entities\Categorie;
use Giftbox\ApplicationCore\Domain\Entities\Coffret;

class Prestation extends Model {
    protected $table = 'prestation';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;


    public function categorie() {
        return $this->belongsTo(Categorie::class, 'cat_id', 'id');
    }

        public function coffrets()
    {
        return $this->belongsToMany(Coffret::class, 'coffret2presta', 'presta_id', 'coffret_id');
    }
}
