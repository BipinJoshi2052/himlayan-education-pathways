<?php

declare(strict_types=1);

namespace App\DTOs\Admin;

final readonly class RoleData
{
    /**
     * @param  array<int, string>  $permissions
     */
    public function __construct(
        public string $name,
        public bool $loginEnabled,
        public array $permissions,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            loginEnabled: (bool) ($data['login_enabled'] ?? false),
            permissions: $data['permissions'] ?? [],
        );
    }
}
