<?php

namespace App\Models;

use App\Traits\Fileable;
use App\Traits\Validatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    use Fileable, HasFactory, Validatable;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'description',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'integer',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function distinctCategories(): array
    {
        return static::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->all();
    }

    public function rules($scenario = null)
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'upload_foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function labels()
    {
        return [
            'name' => 'Nama Menu',
            'category' => 'Kategori',
            'description' => 'Deskripsi',
            'price' => 'Harga',
            'is_active' => 'Status Aktif',
            'upload_foto' => 'Foto',
        ];
    }

    public function getIs_activeValAttribute()
    {
        return User::IS_ACTIVE[$this->is_active];
    }

    public function getPriceRpAttribute()
    {
        return 'Rp'.number_format($this->price, 0, ',', '.');
    }
}
