<?php

namespace App\Policies;

use App\Models\LjkAnswerKey;
use App\Models\User;

class LjkAnswerKeyPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LjkAnswerKey $ljkAnswerKey): bool
    {
        return $user->id === $ljkAnswerKey->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LjkAnswerKey $ljkAnswerKey): bool
    {
        return $user->id === $ljkAnswerKey->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LjkAnswerKey $ljkAnswerKey): bool
    {
        return $user->id === $ljkAnswerKey->user_id;
    }
}
