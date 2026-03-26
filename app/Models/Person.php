<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $fillable = [
        'family_id', 'parent_id', 'name', 'gender', 'photo_path', 'address', 'phone', 'spouse_id',
    ];

    // Ke keluarga besar
    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    // Ke orang tua (1 garis keturunan di atasnya)
    public function parent()
    {
        return $this->belongsTo(Person::class, 'parent_id');
    }

    // Ke anak-anak
    public function children()
    {
        return $this->hasMany(Person::class, 'parent_id');
    }

    // Ke pasangan (hasMany jika mungkin menikah lebih dari 1 kali)
    public function spouses()
    {
        return $this->hasMany(Spouse::class);
    }

    public function parentSpouse()
    {
        return $this->belongsTo(Spouse::class, 'spouse_id');
    }
}
