<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
    use HasFactory;

    protected $hidden = ['iv', 'key'];

    public function orangs()
    {
        return $this->belongsTo(Orang::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
