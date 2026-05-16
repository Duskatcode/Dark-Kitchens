<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    public const ADMIN = 'admin';
    public const CLIENT = 'client';
    public const COOK = 'cook';

    public const DEFAULT_REGISTRATION_ROLE = self::CLIENT;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @return list<string>
     */
    public static function coreRoles(): array
    {
        return [self::ADMIN, self::CLIENT, self::COOK];
    }

    public function isCoreRole(): bool
    {
        return in_array($this->name, self::coreRoles(), true);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }
}
