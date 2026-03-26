<?php

namespace Database\Seeders;

use App\Models\Family;
use App\Models\Person;
use App\Models\Spouse;
use Illuminate\Database\Seeder;

class FamilyTreeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Keluarga Induk
        $keluarga = Family::create([
            'name' => 'Keluarga Mashudi',
            'description' => 'Silsilah keturunan dari Bapak Mashudi',
        ]);

        // 2. Buat Leluhur Pertama (Generasi 1) - Tanpa parent_id
        $kakek = Person::create([
            'family_id' => $keluarga->id,
            'name' => 'Mashudi',
            'gender' => 'L',
        ]);
        // Pasangan Leluhur
        Spouse::create([
            'person_id' => $kakek->id,
            'name' => 'Aminah',
            'gender' => 'P',
        ]);

        // 3. Buat Anak-anak (Generasi 2) - parent_id adalah $kakek->id
        $anak1 = Person::create([
            'family_id' => $keluarga->id,
            'parent_id' => $kakek->id,
            'name' => 'Budi Santoso',
            'gender' => 'L',
        ]);
        Spouse::create([
            'person_id' => $anak1->id,
            'name' => 'Siti',
            'gender' => 'P',
        ]);

        $anak2 = Person::create([
            'family_id' => $keluarga->id,
            'parent_id' => $kakek->id,
            'name' => 'Rina',
            'gender' => 'P',
        ]);
        Spouse::create([
            'person_id' => $anak2->id,
            'name' => 'Andi',
            'gender' => 'L',
        ]);

        // 4. Buat Cucu (Generasi 3)
        // Cucu dari Anak 1
        Person::create([
            'family_id' => $keluarga->id,
            'parent_id' => $anak1->id,
            'name' => 'Doni',
            'gender' => 'L',
        ]);

        // Cucu dari Anak 2
        Person::create([
            'family_id' => $keluarga->id,
            'parent_id' => $anak2->id,
            'name' => 'Maya',
            'gender' => 'P',
        ]);
    }
}
