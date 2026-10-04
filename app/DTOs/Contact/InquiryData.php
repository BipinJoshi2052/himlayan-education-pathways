<?php

declare(strict_types=1);

namespace App\DTOs\Contact;

final readonly class InquiryData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public ?string $subject,
        public string $message,
        public string $ipAddress,
        public ?string $userAgent,
    ) {}

    public static function fromRequest(array $data, string $ipAddress, ?string $userAgent): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            subject: $data['subject'] ?? null,
            message: $data['message'],
            ipAddress: $ipAddress,
            userAgent: $userAgent,
        );
    }
}
