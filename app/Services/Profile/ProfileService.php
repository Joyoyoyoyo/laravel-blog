<?php

namespace App\Services\Profile;

use App\Models\User;

class ProfileService
{
    /**
     * @param  array{name: string, email: string, bio?: string|null}  $data
     */
    public function update(User $user, array $data): User
    {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'bio' => $data['bio'] ?? null,
        ]);

        return $user->fresh();
    }

    public function updatePassword(User $user, string $newPlainPassword): void
    {
        $user->update([
            'password' => $newPlainPassword,
        ]);
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
