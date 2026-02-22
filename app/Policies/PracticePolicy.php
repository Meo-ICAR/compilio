<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Practice;
use Illuminate\Auth\Access\HandlesAuthorization;

class PracticePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Practice');
    }

    public function view(AuthUser $authUser, Practice $practice): bool
    {
        return $authUser->can('View:Practice');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Practice');
    }

    public function update(AuthUser $authUser, Practice $practice): bool
    {
        return $authUser->can('Update:Practice');
    }

    public function delete(AuthUser $authUser, Practice $practice): bool
    {
        return $authUser->can('Delete:Practice');
    }

    public function restore(AuthUser $authUser, Practice $practice): bool
    {
        return $authUser->can('Restore:Practice');
    }

    public function forceDelete(AuthUser $authUser, Practice $practice): bool
    {
        return $authUser->can('ForceDelete:Practice');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Practice');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Practice');
    }

    public function replicate(AuthUser $authUser, Practice $practice): bool
    {
        return $authUser->can('Replicate:Practice');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Practice');
    }

}