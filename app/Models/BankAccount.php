<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'bankable_type',
        'bankable_id',
        'iban',
        'bank_name',
        'bic_swift',
        'opened_at',
        'is_dedicated',
        'provider_account_id',
        'balance',
        'last_synced_at',
        'company_id'
    ];

    protected $casts = [
        'opened_at' => 'date',
        'is_dedicated' => 'boolean',
        'balance' => 'decimal:2',
        'last_synced_at' => 'datetime',
    ];

    /**
     * Get the parent bankable model (Client, Principal, etc.)
     */
    public function bankable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get all transactions for this bank account
     */
    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    /**
     * Get all bank statements for this account
     */
    public function bankStatements(): MorphMany
    {
        return $this->morphMany(BankStatement::class, 'statementable');
    }

    /**
     * Scope a query to only include dedicated accounts
     */
    public function scopeDedicated($query)
    {
        return $query->where('is_dedicated', true);
    }

    /**
     * Scope a query to only include general accounts
     */
    public function scopeGeneral($query)
    {
        return $query->where('is_dedicated', false);
    }

    /**
     * Scope a query to only include synchronized accounts
     */
    public function scopeSynchronized($query)
    {
        return $query->whereNotNull('last_synced_at');
    }

    /**
     * Get formatted IBAN
     */
    public function getFormattedIbanAttribute(): string
    {
        $iban = $this->iban;
        if (strlen($iban) === 27) {
            // Italian IBAN format: ITXX X XXXX XXXX XXXX XXXX XXXX XXX
            return substr($iban, 0, 2) . ' '
                . substr($iban, 2, 1) . ' '
                . substr($iban, 3, 5) . ' '
                . substr($iban, 8, 5) . ' '
                . substr($iban, 13, 5) . ' '
                . substr($iban, 18, 3) . ' '
                . substr($iban, 21, 4) . ' '
                . substr($iban, 25, 2);
        }

        return $iban;
    }

    /**
     * Check if account is synchronized
     */
    public function isSynchronized(): bool
    {
        return !is_null($this->last_synced_at);
    }

    /**
     * Get account type label
     */
    public function getAccountTypeLabelAttribute(): string
    {
        return $this->is_dedicated ? 'Dedicato' : 'Generale';
    }

    /**
     * Get bank name with BIC
     */
    public function getBankNameWithBicAttribute(): string
    {
        $name = $this->bank_name ?? 'N/A';
        $bic = $this->bic_swift ?? '';

        return $bic ? "{$name} ({$bic})" : $name;
    }
}
