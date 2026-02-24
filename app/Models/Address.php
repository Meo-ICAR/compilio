<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'name',
        'numero',
        'street',
        'city',
        'zip_code',
        'address_type_id',
        'addressable_type',
        'addressable_id',
    ];

    public function addressable()
    {
        return $this->morphTo();
    }

    public function getFullAddressAttribute(): string
    {
        $parts = [];

        if ($this->street) {
            $parts[] = $this->street;
        }

        if ($this->numero) {
            $parts[] = $this->numero;
        }

        if ($this->city) {
            $parts[] = $this->city;
        }

        if ($this->zip_code) {
            $parts[] = $this->zip_code;
        }

        return implode(', ', $parts);
    }

    public function getStreetWithNumberAttribute(): string
    {
        $street = $this->street ?? '';
        $numero = $this->numero ?? '';

        if ($street && $numero) {
            return $street . ' ' . $numero;
        }

        return $street . $numero;
    }
}
