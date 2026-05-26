<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;

class MediaPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'editor']);
    }

    public function view(User $user, Media $media): bool
    {
        return in_array($user->role, ['admin', 'editor']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'editor']);
    }

    public function delete(User $user, Media $media): bool
    {
        // Admins can delete any; editors can only delete their own
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'editor' && $media->user_id === $user->id;
    }
}
