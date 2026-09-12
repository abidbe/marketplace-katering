<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\Fileable;
use App\Traits\Validatable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use Fileable, HasFactory, Notifiable, Validatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'company_name',
        'description',
        'address',
        'city',
        'phone',
    ];

    public const ROLE = [
        'admin' => 'Admin',
        'merchant' => 'Merchant',
        'customer' => 'Customer',
    ];

    public const IS_ACTIVE = [
        true => 'Aktif',
        false => 'Tidak Aktif',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function rules($scenario = null)
    {
        $scenarios = [
            null => [
                'password' => [
                    'required',
                    'string',
                    Password::min(8)
                        ->numbers()
                        ->symbols(),
                    'max:256',
                ],
                'password_confirmation' => 'required|same:password',
                'name' => 'required|max:256',
                'email' => [
                    'required',
                    Rule::unique($this->getTable())->ignore($this),
                    'email',
                    'max:256',
                ],
                'role' => 'required|in:'.implode(',', array_keys(self::ROLE)),
                'is_active' => 'required|boolean',
                'phone' => 'nullable|max:20',
                'company_name' => 'nullable|max:256',
                'address' => 'nullable|max:500',
                'city' => 'nullable|max:256',
                'description' => 'nullable|max:2000',
            ],
            'edit' => [
                'password' => [
                    'nullable',
                    'string',
                    Password::min(8)
                        ->numbers()
                        ->symbols(),
                    'max:256',
                ],
                'password_confirmation' => 'nullable|same:password',
            ],
        ];

        $rules = $scenarios[$scenario] ?? $scenarios[null];

        if ($scenario === 'edit') {
            $rules = array_merge($scenarios[null], $scenarios['edit']);
        }

        return $rules;
    }

    public function labels()
    {
        return [
            'name' => 'Nama',
            'email' => 'Email',
            'password' => 'Password',
            'role' => 'Role',
            'is_active' => 'Status Aktif',
            'company_name' => 'Nama Perusahaan',
            'description' => 'Deskripsi',
            'address' => 'Alamat',
            'city' => 'Kota',
            'phone' => 'No Telepon',
        ];
    }

    public function profileRules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
        ];
    }

    public function getIs_activeValAttribute()
    {
        return self::IS_ACTIVE[$this->is_active];
    }

    public function getRoleValAttribute()
    {
        return self::ROLE[$this->role];
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }
}
