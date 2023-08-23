<?php

namespace App\Models;

use App\Models\Scopes\IsActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model {
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id', 'name', 'description'];

    protected static function booted() {
        parent::booted();
        self::addGlobalScope(new IsActiveScope());
    }

    public function products(): HasMany {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function cheapestProduct(): HasOne {
        return $this->hasOne(Product::class)->ofMany('price', 'min');
    }

    public function mostExpensiveProduct(): HasOne {
        return $this->hasOne(Product::class)->ofMany('price', 'max');
    }

    public function reviews(): HasManyThrough {
        return $this->hasManyThrough(
            Review::class,
            Product::class,
            'category_id',   // FK on products table
            'product_id',   // FK on reviews table
            'id',   // PK on categories table
            'id'    // PK on prducts table
        );
    }
}
