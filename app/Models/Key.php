<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
    use HasFactory;

    protected $hidden = ['key', 'type'];

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
