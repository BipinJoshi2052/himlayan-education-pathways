<?php

declare(strict_types=1);

namespace App\DTOs\Admin;

final readonly class UserData
{
    /**
     * password is null on an update when the admin left the field blank —
     * keeps the existing password rather than wiping it, same pattern as
     * the SMTP password setting (see SettingController).
     *
     * @param  array<int, string>  $roles
     */
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password,
        public array $roles,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: filled($data['password'] ?? null) ? $data['password'] : null,
            roles: $data['roles'] ?? [],
        );
    }
}
