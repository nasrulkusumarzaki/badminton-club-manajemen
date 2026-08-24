<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Atlet;
use App\Models\User;

class AbsensiAtlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'atlet_id',
        'status',
        'dicatat_oleh',
    ];

    public function atlet()
    {
        return $this->belongsTo(Atlet::class);
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
