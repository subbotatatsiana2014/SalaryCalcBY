<?php

namespace App\Models;

use App\Models\Employee\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

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
        'phone',
        'birth_date',
        'address',
        'gender',
        'avatar',
        'last_login_at',
    ];

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
        'last_login_at' => 'datetime',
    ];

    /**
     * Проверка роли администратора
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Проверка роли бухгалтера
     */
    public function isAccountant(): bool
    {
        return $this->role === 'accountant';
    }

    /**
     * Проверка роли кадровика
     */
    public function isHr(): bool
    {
        return $this->role === 'hr';
    }

    /**
     * Проверка роли начальника
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Получить доступные роли
     */
    public static function getAvailableRoles(): array
    {
        return [
            'admin' => 'Администратор',
            'accountant' => 'Бухгалтер',
            'hr' => 'Кадровик',
            'manager' => 'Начальник подразделения',
        ];
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }



    // Геттер для имени
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getFirstNameAttribute()
    {
        $parts = explode(' ', $this->name);
        return $parts[1] ?? '';
    }

    public function getLastNameAttribute()
    {
        $parts = explode(' ', $this->name);
        return $parts[0] ?? '';
    }

    public function getMiddleNameAttribute()
    {
        $parts = explode(' ', $this->name);
        return $parts[2] ?? '';
    }

    public function getRoleNameAttribute(): string
{
    return self::getAvailableRoles()[$this->role] ?? $this->role;
}

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }

        $name = $this->name;
        return 'https://ui-avatars.com/api/?background=3b82f6&color=fff&name=' . urlencode($name);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'name' => $this->name,
        ];
    }
}
