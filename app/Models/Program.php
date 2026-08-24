<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_program',
        'deskripsi',
        'jenis',
        'tanggal',
        'level',
    ];

    /**
     * Cast attributes to proper types.
     */
    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Atlet yang ter-assign ke program ini.
     */
    public function atlets()
    {
        return $this->belongsToMany(\App\Models\Atlet::class, 'atlet_program', 'program_id', 'atlet_id')
                    ->withTimestamps();
    }
}
