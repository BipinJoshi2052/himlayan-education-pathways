<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\DTOs\Admin\UserData;
use App\Models\User;

final readonly class SaveUserAction
{
    public function handle(UserData $data, ?User $user = null): User
    {
        $user ??= new User;

        $user->name = $data->name;
        $user->email = $data->email;

        if ($data->password !== null) {
            // The 'password' => 'hashed' cast on User hashes this
            // automatically on save — never hash it here too.
            $user->password = $data->password;
        }

        $user->save();

        $user->syncRoles($data->roles);

        return $user;
    }
}
