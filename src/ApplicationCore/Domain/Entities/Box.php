<?php

namespace Giftbox\ApplicationCore\Domain\Entities;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Box extends Model {

    use HasUuids;
    protected $table = 'box';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'token',
        'libelle',
        'description',
        'montant',
        'kdo',
        'message_kdo',
        'statut',
        'created_at',
        'updated_at',
        'createur_id',
    ];
    protected $casts = [
        'montant' => 'decimal:2',
        'kdo' => 'boolean',
        'statut' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function prestations()
    {
        return $this->belongsToMany(
            Prestation::class,      // Modèle lié
            'box2presta',          // Table pivot
            'box_id',              // clé étrangère pour Box dans pivot
            'presta_id'            // clé étrangère pour Prestation dans pivot
        )->withPivot('quantite');   // récupère aussi la colonne quantite
    }
}
