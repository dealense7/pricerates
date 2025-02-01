<?php

declare(strict_types=1);

namespace App\Support\Traits;

/**
 * @property \Carbon\Carbon|null created_at
 * @property \Carbon\Carbon|null updated_at
 */
trait HasDefaultDates
{
    public function getCreatedAt(): ?string
    {
        return $this->created_at?->format('Y-m-d H:i:s');
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at?->format('Y-m-d H:i:s');
    }
}
