<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pharmacist extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'license_number',
        'specialization',
        'hire_date',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hire_date' => 'date',
    ];

    /**
     * Obtenir les ventes associées à ce pharmacien.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
