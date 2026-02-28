<?php

namespace App\Models;

use App\Models\ChecklistDocument;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

class Practice extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'client_mandate_id',
        'company_id',
        'principal_id',
        'agent_id',
        'practice_status_id',
        'stato_pratica',
        'name',
        'CRM_code',
        'principal_code',
        'amount',
        'net',
        'brokerage_fee',
        'practice_scope_id',
        'status',
        'statoproforma',
        'inserted_at',
        'erogated_at',
        'rejected_at',
        'rejected_reason',
        'status_at',
        'description',
        'annotation',
        'perfected_at',
        'is_active',
    ];

    protected $appends = ['clients_names'];

    protected $casts = [
        'status' => \App\Enums\PracticeStatus::class,
        'perfected_at' => 'date',
        'inserted_at' => 'date',
        'erogated_at' => 'date',
        'rejected_at' => 'date',
        'status_at' => 'date',
        'amount' => 'decimal:2',
        'net' => 'decimal:2',
        'brokerage_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_practice')->withPivot(['role', 'name', 'notes'])->withTimestamps();
    }

    public function practiceCommissions()
    {
        return $this->hasMany(PracticeCommission::class);
    }

    public function principal()
    {
        return $this->belongsTo(Principal::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function practiceScope()
    {
        return $this->belongsTo(PracticeScope::class);
    }

    public function clientMandate()
    {
        return $this->belongsTo(ClientMandate::class);
    }

    public function practiceStatus()
    {
        return $this->belongsTo(PracticeStatus::class);
    }

    public function getClientsNamesAttribute()
    {
        $clients = \DB::table('clients')
            ->join('client_practice', 'clients.id', '=', 'client_practice.client_id')
            ->where('client_practice.practice_id', $this->id)
            ->where('clients.company_id', $this->company_id)
            ->pluck('clients.name');

        return $clients->join(', ');
    }

    /**
     * Calcola lo stato della checklist
     */
    public function getChecklist(): Collection
    {
        // 1. Recupera i requisiti per questo scope e questa banca (o requisiti generali)
        $requirements = ChecklistDocuments::where('practice_scope_id', $this->practice_scope_id)
            ->where(function ($query) {
                $query
                    ->where('principal_id', $this->principal_id)
                    ->orWhereNull('principal_id');
            })
            ->with('documentType')
            ->get();

        // 2. Recupera i tipi di documenti già caricati
        $uploadedDocumentTypeIds = $this
            ->getMedia('documents')
            ->map(fn($media) => (int) $media->getCustomProperty('document_type_id'))
            ->unique();

        // 3. Costruisce la lista
        return $requirements->map(function ($req) use ($uploadedDocumentTypeIds) {
            return (object) [
                'name' => $req->documentType->name,
                'is_required' => $req->is_required,
                'description' => $req->description,
                'is_uploaded' => $uploadedDocumentTypeIds->contains($req->document_type_id),
            ];
        });
    }

    public function isWorkingStatus()
    {
        return $this->practiceStatus?->is_working ?? false;
    }

    public function isRejectedStatus()
    {
        return $this->practiceStatus?->is_rejected ?? false;
    }

    public function isPerfectedStatus()
    {
        return !empty($this->perfected_at);
    }

    public function checkWorkingLastYear()
    {
        $lastYear = now()->subYear()->endOfYear();
        if (!$this->checkRejectedLastYear() || !$this->checkPerfectedLastYear()) {
            return false;
        }
        return $this->practiceStatus?->is_working && $this->inserted_at < $lastYear;
    }

    public function checkRejectedLastYear()
    {
        $lastYear = now()->subYear()->endOfYear();
        $startYear = now()->subYear()->startOfYear();
        if (!$this->practiceStatus?->is_rejectedStatus()) {
            return false;
        }
        return $this->inserted_at < $lastYear &&
            $this->rejected_at < $lastYear &&
            $this->rejected_at > $startYear ?? false;
    }

    public function checkPerfectedLastYear()
    {
        $lastYear = now()->subYear()->endOfYear();
        $startYear = now()->subYear()->startOfYear();
        return $this->practiceStatus?->is_perfectedStatus() &&
            $this->inserted_at < $lastYear &&
            $this->perfected_at > $startYear &&
            $this->perfected_at < $lastYear ?? false;
    }

    public function OAMisLastYearStatus()
    {
        return $this->isOAMname() && (
            $this->checkWorkingLastYear() ||
            $this->checkPerfectedLastYear() ||
            $this->checkRejectedLastYear()
        );
    }

    public function practiceScopeOAM()
    {
        return $this->practiceScope->oamname();
    }

    public function scopeWithOamScopeAndConditions($query)
    {
        return $query
            ->whereHas('practiceScope', function ($query) {
                $query->whereNotNull('oam_code');
            })
            ->where(function ($query) {
                // Condizione 1: isWorking() true e inserted_at < fine anno precedente
                $query
                    ->whereHas('practiceStatus', function ($statusQuery) {
                        $statusQuery->where('is_working', true);
                    })
                    ->where('inserted_at', '<', now()->subYear()->endOfYear())
                    // OR
                    // Condizione 2: perfected_at null OR perfected_at > fine anno precedente
                    ->orWhere(function ($perfectedQuery) {
                        $perfectedQuery
                            ->whereNull('perfected_at')
                            ->orWhere('perfected_at', '>', now()->subYear()->endOfYear());
                    });
            });
    }
}
