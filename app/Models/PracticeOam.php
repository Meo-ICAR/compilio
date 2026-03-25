<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeOam extends Model
{
    protected $fillable = [
        'id',
        'practice_id',
        'oam_code_id',
        'oam_code',
        'oam_name',
        'principal_name',
        'is_notconvenctioned',
        'is_previous',
        'liquidato',
        'liquidato_lavorazione',
        'CRM_code',
        'practice_name',
        'type',
        'inserted_at',
        'erogated_at',
        'compenso',
        'compenso_lavorazione',
        'erogato',
        'erogato_lavorazione',
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
        'is_cancel',
        'is_perfected',
        'is_conventioned',
        'is_notconventioned',
        'is_working',
        'invoice_at',
        'start_date',
        'perfected_at',
        'end_date',
        'accepted_at',
        'canceled_at',
        'is_invoice',
        'is_before',
        'is_after',
        'name',
        'tipo_prodotto',
        'mese',
        'company_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'practice_id' => 'integer',
        'oam_code_id' => 'integer',
        'liquidato' => 'decimal:2',
        'liquidato_lavorazione' => 'decimal:2',
        'compenso' => 'decimal:2',
        'compenso_lavorazione' => 'decimal:2',
        'erogato' => 'decimal:2',
        'erogato_lavorazione' => 'decimal:2',
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
        'is_cancel' => 'boolean',
        'is_perfected' => 'boolean',
        'is_conventioned' => 'boolean',
        'is_notconventioned' => 'boolean',
        'is_working' => 'boolean',
        'is_notconvenctioned' => 'boolean',
        'is_previous' => 'boolean',
        'is_invoice' => 'boolean',
        'is_before' => 'boolean',
        'is_after' => 'boolean',
        'mese' => 'integer',
        'company_id' => 'string',
        'oam_code' => 'string',
        'oam_name' => 'string',
        'principal_name' => 'string',
        'CRM_code' => 'string',
        'practice_name' => 'string',
        'type' => 'string',
        'name' => 'string',
        'tipo_prodotto' => 'string',
        'inserted_at' => 'date',
        'erogated_at' => 'date',
        'invoice_at' => 'date',
        'start_date' => 'date',
        'perfected_at' => 'date',
        'end_date' => 'date',
        'accepted_at' => 'date',
        'canceled_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
