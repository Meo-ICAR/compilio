<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeOam extends Model
{
    protected $fillable = [
        'company_id',
        'practice_id',
        'oam_code_id',
        'compenso',
        'compenso_lavorazione',
        'compenso_premio',
        'compenso_rimborso',
        'compenso_assicurazione',
        'compenso_cliente',
        'storno',
        'provvigione',
        'provvigione_lavorazione',
        'provvigione_premio',
        'provvigione_rimborso',
        'provvigione_assicurazione',
        'provvigione_storno',
    ];

    protected $casts = [
        'compenso' => 'decimal:2',
        'compenso_lavorazione' => 'decimal:2',
        'compenso_premio' => 'decimal:2',
        'compenso_rimborso' => 'decimal:2',
        'compenso_assicurazione' => 'decimal:2',
        'compenso_cliente' => 'decimal:2',
        'storno' => 'decimal:2',
        'provvigione' => 'decimal:2',
        'provvigione_lavorazione' => 'decimal:2',
        'provvigione_premio' => 'decimal:2',
        'provvigione_rimborso' => 'decimal:2',
        'provvigione_assicurazione' => 'decimal:2',
        'provvigione_storno' => 'decimal:2',
    ];

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function oamCode()
    {
        return $this->belongsTo(OamCode::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
