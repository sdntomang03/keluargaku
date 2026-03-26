<?php

namespace Database\Seeders;

use App\Models\Family;
use App\Models\Person;
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

        // ==================================================
        // GENERASI 1 (LELUHUR)
        // ==================================================

        // Buat Kakek (Bapak Mashudi)
        $kakek = Person::create([
            'family_id' => $keluarga->id,
            'name' => 'Mashudi',
            'gender' => 'L',
        ]);

        // Buat Nenek (Istri Mashudi)
        $nenek = Person::create([
            'family_id' => $keluarga->id,
            'name' => 'Aminah',
            'gender' => 'P',
        ]);

        // Ikat Pernikahan Mereka (Bi-directional)
        $kakek->spouses()->attach($nenek->id);
        $nenek->spouses()->attach($kakek->id);

        // ==================================================
        // GENERASI 2 (ANAK-ANAK)
        // ==================================================

        // Anak 1: Budi Santoso (L)
        $anak1 = Person::create([
            'family_id' => $keluarga->id,
            'father_id' => $kakek->id, // Ayahnya Mashudi
            'mother_id' => $nenek->id, // Ibunya Aminah
            'name' => 'Budi Santoso',
            'gender' => 'L',
        ]);

        // Istri Budi
        $istriBudi = Person::create([
            'family_id' => $keluarga->id,
            'name' => 'Siti',
            'gender' => 'P',
        ]);

        // Ikat Pernikahan Budi & Siti
        $anak1->spouses()->attach($istriBudi->id);
        $istriBudi->spouses()->attach($anak1->id);

        // Anak 2: Rina (P)
        $anak2 = Person::create([
            'family_id' => $keluarga->id,
            'father_id' => $kakek->id, // Ayahnya Mashudi
            'mother_id' => $nenek->id, // Ibunya Aminah
            'name' => 'Rina',
            'gender' => 'P',
        ]);

        // Suami Rina
        $suamiRina = Person::create([
            'family_id' => $keluarga->id,
            'name' => 'Andi',
            'gender' => 'L',
        ]);

        // Ikat Pernikahan Rina & Andi
        $anak2->spouses()->attach($suamiRina->id);
        $suamiRina->spouses()->attach($anak2->id);

        // ==================================================
        // GENERASI 3 (CUCU-CUCU)
        // ==================================================

        // Cucu 1: Doni (Anak dari Budi & Siti)
        Person::create([
            'family_id' => $keluarga->id,
            'father_id' => $anak1->id,    // Ayahnya Budi
            'mother_id' => $istriBudi->id, // Ibunya Siti
            'name' => 'Doni',
            'gender' => 'L',
        ]);

        // Cucu 2: Maya (Anak dari Andi & Rina)
        Person::create([
            'family_id' => $keluarga->id,
            'father_id' => $suamiRina->id, // Ayahnya Andi
            'mother_id' => $anak2->id,     // Ibunya Rina
            'name' => 'Maya',
            'gender' => 'P',
        ]);
    }
}
