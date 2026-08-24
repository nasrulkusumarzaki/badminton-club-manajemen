<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Atlet extends Model
{
    use HasFactory;

    protected $fillable= [
        'nama',
        'umur',
        'jenis_kelamin',
        'no_hp',
        'level',
        'foto',
    ];

    /**
     * Label level yang enak dibaca (Pemula / Beginner / Senior).
     */
    public function levellabel(): string
    {
        return match ($this->level) {
            'pemula' => 'Pemula',
            'beginner' => 'Beginner',
            'senior' => 'Senior',
            default => '$this->level'
        };
    }
    /**
     *  URL foto atlet, atau null jika belum ada foto.
     */
    public function fotoUrl(): ?string
    {
        return $this->foto ? Storage::url($this->foto) : null;
    }

    /**
     * Programs yang di-assign ke atlet ini.
     */
    public function programs()
    {
        return $this->belongsToMany(\App\Models\Program::class, 'atlet_program', 'atlet_id', 'program_id')
                    ->withTimestamps();
    }
}
