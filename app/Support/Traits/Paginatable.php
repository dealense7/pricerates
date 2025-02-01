<?php

declare(strict_types=1);

namespace App\Support\Traits;

use Illuminate\Validation\ValidationException;

/**
 * @mixin \App\Models\Model
 */
trait Paginatable
{
    public function getMaxPerPage(): int
    {
        return $this->maxPerPage;
    }

    public function getValidPerPage(?int $perPage = null): int
    {
        if (empty($perPage)) {
            return $this->getPerPage();
        }

        if ($perPage > $this->getMaxPerPage()) {
            throw ValidationException::withMessages(['perPage' => 'მოცემულ გვერდზე მონაცემების მაქსიმალური რაოდენობაა: ' . $this->getMaxPerPage()]);
        }

        return $perPage;
    }
}
