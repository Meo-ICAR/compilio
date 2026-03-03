<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rui extends Model
{
    use HasFactory;

    protected $table = 'rui';

    protected $fillable = [
        'oss',
        'inoperativo',
        'data_inizio_inoperativita',
        'numero_iscrizione_rui',
        'data_iscrizione',
        'cognome_nome',
        'stato',
        'comune_nascita',
        'data_nascita',
        'ragione_sociale',
        'provincia_nascita',
        'titolo_individuale_sez_a',
        'attivita_esercitata_sez_a',
        'titolo_individuale_sez_b',
        'attivita_esercitata_sez_b',
        'rui_section_id',
    ];

    protected $casts = [
        'inoperativo' => 'boolean',
        'data_inizio_inoperativita' => 'date',
        'data_iscrizione' => 'date',
        'data_nascita' => 'date',
    ];

    public function ruiSection()
    {
        return $this->belongsTo(RuiSection::class);
    }
}
