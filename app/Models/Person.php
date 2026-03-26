<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $fillable = [
        'family_id', 'father_id', 'mother_id', 'name',
        'gender', 'phone', 'address', 'photo_path',
    ];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function father()
    {
        return $this->belongsTo(Person::class, 'father_id');
    }

    public function mother()
    {
        return $this->belongsTo(Person::class, 'mother_id');
    }

    // Mengambil semua anak (di mana dia jadi bapak ATAU ibu)
    public function children()
    {
        return Person::where('father_id', $this->id)
            ->orWhere('mother_id', $this->id)->get();
    }

    // Mengambil semua pasangan (Suami/Istri) melalui tabel marriages
    public function spouses()
    {
        return $this->belongsToMany(Person::class, 'marriages', 'person_id', 'spouse_id');
    }
}
