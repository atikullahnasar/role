<?php

namespace atikullahnasar\role\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = ['beft_permissions'];
    protected $fillable = ['name', 'group_name'];
}
