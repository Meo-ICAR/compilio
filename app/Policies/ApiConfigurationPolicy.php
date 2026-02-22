<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ApiConfiguration;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApiConfigurationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ApiConfiguration');
    }

    public function view(AuthUser $authUser, ApiConfiguration $apiConfiguration): bool
    {
        return $authUser->can('View:ApiConfiguration');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ApiConfiguration');
    }

    public function update(AuthUser $authUser, ApiConfiguration $apiConfiguration): bool
    {
        return $authUser->can('Update:ApiConfiguration');
    }

    public function delete(AuthUser $authUser, ApiConfiguration $apiConfiguration): bool
    {
        return $authUser->can('Delete:ApiConfiguration');
    }

    public function restore(AuthUser $authUser, ApiConfiguration $apiConfiguration): bool
    {
        return $authUser->can('Restore:ApiConfiguration');
    }

    public function forceDelete(AuthUser $authUser, ApiConfiguration $apiConfiguration): bool
    {
        return $authUser->can('ForceDelete:ApiConfiguration');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ApiConfiguration');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ApiConfiguration');
    }

    public function replicate(AuthUser $authUser, ApiConfiguration $apiConfiguration): bool
    {
        return $authUser->can('Replicate:ApiConfiguration');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ApiConfiguration');
    }

}