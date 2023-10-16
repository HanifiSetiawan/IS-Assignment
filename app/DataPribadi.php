<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPribadi extends Model
{
    protected $fillable = [
        'nama', 'nomor_telepon', 'foto_ktp', 'file_pdf', 'file_doc', 'file_xls', 'video',
    ];
}
