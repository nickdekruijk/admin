<?php

namespace NickDeKruijk\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use NickDeKruijk\Admin\Helpers;

class Permission extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'create' => 'boolean',
        'read' => 'boolean',
        'update' => 'boolean',
        'delete' => 'boolean',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('admin.table_prefix') . 'permissions';
    }

    public function user()
    {
        return $this->belongsTo(Helpers::userModel()::class);
    }

    public function scopeAny($query)
    {
        return $query->where('create', true)
            ->orWhere('read', true)
            ->orWhere('update', true)
            ->orWhere('delete', true);
    }

    public function scopeCurrentUser($query)
    {
        return $query->where('user_id', Auth::guard(config('admin.guard'))->user()->id);
    }

    public function scopeCanAny($query, string $module)
    {
        return $query->any()->where('module', $module);
    }

    public function scopeCanCreate($query, string $module)
    {
        return $query->where('create', true)->where('module', $module);
    }

    public function scopeCanRead($query, string $module)
    {
        return $query->where('read', true)->where('module', $module);
    }

    public function scopeCanUpdate($query, string $module)
    {
        return $query->where('update', true)->where('module', $module);
    }

    public function scopeCanDelete($query, string $module)
    {
        return $query->where('delete', true)->where('module', $module);
    }
}
