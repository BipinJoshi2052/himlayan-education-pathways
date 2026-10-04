<?php

declare(strict_types=1);

namespace App\Events;

use App\Modules\Service\Models\Service;

final class ServiceSaved
{
    public function __construct(public readonly Service $service) {}
}
