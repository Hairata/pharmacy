<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock_quantity',
        'expiry_date',
        'category',
        'manufacturer',
        'image_path',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    /**
     * Obtenir les ventes associées à ce produit.
     */
    public function sales(): BelongsToMany
    {
        return $this->belongsToMany(Sale::class, 'product_sale')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }
}
