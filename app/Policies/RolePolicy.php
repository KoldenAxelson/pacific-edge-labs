<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorizes user actions on roles.
 */
class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Authorizes viewing all roles.
     */
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Role');
    }

    /**
     * Authorizes viewing a specific role.
     */
    public function view(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('View:Role');
    }

    /**
     * Authorizes creating a new role.
     */
    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Role');
    }

    /**
     * Authorizes updating a role.
     */
    public function update(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('Update:Role');
    }

    /**
     * Authorizes deleting a role.
     */
    public function delete(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('Delete:Role');
    }

    /**
     * Authorizes restoring a deleted role.
     */
    public function restore(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('Restore:Role');
    }

    /**
     * Authorizes force deleting a role.
     */
    public function forceDelete(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('ForceDelete:Role');
    }

    /**
     * Authorizes force deleting any role.
     */
    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Role');
    }

    /**
     * Authorizes restoring any deleted role.
     */
    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Role');
    }

    /**
     * Authorizes replicating a role.
     */
    public function replicate(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('Replicate:Role');
    }

    /**
     * Authorizes reordering roles.
     */
    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Role');
    }

}