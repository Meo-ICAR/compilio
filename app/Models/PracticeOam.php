<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeOam extends Model
{
    protected $fillable = [
        'company_id',
        'practice_id',
        'oam_code_id',
        'oam_code',
        'erogato',
        'erogato_lavorazione',
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
        'is_active',
        'is_perfected',
        'is_conventioned',
        'is_notconventioned',
        'inserted_at',
        'invoice_at',
        'is_invoice',
        'start_date',
        'perfected_at',
        'end_date',
        'is_notconvenctioned',
        'name',
        'tipo_prodotto',
        'mese',
        'is_working',
        'accepted_at',
        'is_cancel',
        'canceled_at'
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
        'is_active' => 'boolean',
        'is_perfected' => 'boolean',
        'is_conventioned' => 'boolean',
        'is_notconventioned' => 'boolean',
        'is_invoice' => 'boolean',
        'is_notconvenctioned' => 'boolean',
        'is_working' => 'boolean',
        'inserted_at' => 'date',
        'invoice_at' => 'date',
        'perfected_at' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'accepted_at' => 'date',
        'name' => 'string',
        'tipo_prodotto' => 'string',
        'mese' => 'integer',
        'erogato' => 'decimal:2',
        'is_cancel' => 'boolean',
        'canceled_at' => 'date',
    ];

    public function is_notperfected()
    {
        return !$this->is_perfected;
    }

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
