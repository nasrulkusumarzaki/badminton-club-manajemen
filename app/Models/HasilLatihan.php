<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilLatihan extends Model
{
    use HasFactory;

    protected $table = 'hasil_latihan';

    protected $fillable = [
        'atlet_id',
        'program_id',
        'tanggal',
        'nilai_set_1', 'nilai_set_2', 'nilai_set_3', 'nilai_set_4', 'nilai_set_5',
        'nilai_set_6', 'nilai_set_7', 'nilai_set_8', 'nilai_set_9', 'nilai_set_10', 'nilai_set_11',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nilai_set_1' => 'float',
        'nilai_set_2' => 'float',
        'nilai_set_3' => 'float',
        'nilai_set_4' => 'float',
        'nilai_set_5' => 'float',
        'nilai_set_6' => 'float',
        'nilai_set_7' => 'float',
        'nilai_set_8' => 'float',
        'nilai_set_9' => 'float',
        'nilai_set_10' => 'float',
        'nilai_set_11' => 'float',
    ];

    public function atlet()
    {
        return $this->belongsTo(Atlet::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
