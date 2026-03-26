<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spouse extends Model
{
    protected $fillable = ['person_id', 'name', 'gender', 'photo_path'];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
