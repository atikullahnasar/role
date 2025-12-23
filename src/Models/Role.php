<?php

namespace atikullahnasar\role\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'beft_roles';
    protected $fillable = ['name', 'owner_id'];


    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
