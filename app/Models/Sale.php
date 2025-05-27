<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sale extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'pharmacist_id',
        'sale_date',
        'total_amount',
        'payment_method',
        'status',
        'notes',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sale_date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Obtenir le client associé à cette vente.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Obtenir le pharmacien associé à cette vente.
     */
    public function pharmacist(): BelongsTo
    {
        return $this->belongsTo(Pharmacist::class);
    }

    /**
     * Obtenir les produits associés à cette vente.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_sale')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }
}
