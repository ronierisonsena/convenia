<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserType;

class CollaboratorPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->type->id == UserType::TYPE_MANAGER;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $collaborator): bool
    {
        return $collaborator?->staff?->manager_id === $user->manager->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function destroy(User $user, User $collaborator): bool
    {
        return $collaborator->staff->manager_id === $user->manager->id;
    }

    /**
     * Determine whether the user can import models.
     */
    public function importCsv(User $user): bool
    {
        return $user->type->id == UserType::TYPE_MANAGER;
    }
}
