<?php

namespace NickDeKruijk\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
