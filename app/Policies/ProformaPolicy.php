<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Proforma;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProformaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Proforma');
    }

    public function view(AuthUser $authUser, Proforma $proforma): bool
    {
        return $authUser->can('View:Proforma');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Proforma');
    }

    public function update(AuthUser $authUser, Proforma $proforma): bool
    {
        return $authUser->can('Update:Proforma');
    }

    public function delete(AuthUser $authUser, Proforma $proforma): bool
    {
        return $authUser->can('Delete:Proforma');
    }

    public function restore(AuthUser $authUser, Proforma $proforma): bool
    {
        return $authUser->can('Restore:Proforma');
    }

    public function forceDelete(AuthUser $authUser, Proforma $proforma): bool
    {
        return $authUser->can('ForceDelete:Proforma');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Proforma');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Proforma');
    }

    public function replicate(AuthUser $authUser, Proforma $proforma): bool
    {
        return $authUser->can('Replicate:Proforma');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Proforma');
    }

}