<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'pesel',
        'password',
    ];

    const STATUS_VERIFY = 'VERIFY';
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    /**
     * Sprawdź czy użytkownik jest adminem
     */
    public function isAdmin(): bool
    {
        return $this->admin !== null;
    }

    public function canLogin(): bool
        {
            return $this->status === self::STATUS_ACTIVE;
        }

    /**
 * Sprawdź czy użytkownik czeka na weryfikację
 */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_VERIFY;
    }

    /**
     * Sprawdź czy użytkownik jest aktywny
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Aktywuj użytkownika
     */
    public function activate()
    {
        $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Dezaktywuj użytkownika
     */
    public function deactivate()
    {
        $this->update(['status' => self::STATUS_INACTIVE]);
    }
}