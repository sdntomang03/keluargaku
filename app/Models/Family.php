<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    protected $fillable = ['name', 'description'];

    public function people()
    {
        return $this->hasMany(Person::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
