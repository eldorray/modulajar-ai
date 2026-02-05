<?php

namespace App\Policies;

use App\Models\LjkTemplate;
use App\Models\User;

class LjkTemplatePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LjkTemplate $ljkTemplate): bool
    {
        return $user->id === $ljkTemplate->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LjkTemplate $ljkTemplate): bool
    {
        return $user->id === $ljkTemplate->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LjkTemplate $ljkTemplate): bool
    {
        return $user->id === $ljkTemplate->user_id;
    }
}
