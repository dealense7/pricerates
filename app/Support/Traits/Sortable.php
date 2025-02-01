<?php

declare(strict_types=1);

namespace App\Support\Traits;

use function explode;
use function in_array;
use function substr;
use function trim;

/**
 * @mixin \App\Models\Model
 */
trait Sortable
{
    protected array $sortFields = [];
    protected array $sortBy = [];

    public function getSortFields(): array
    {
        return $this->sortFields;
    }

    public function parseSort(?string $sort = null): array
    {
        $explode = explode(',', (string) $sort);

        $sorts = [];
        foreach ($explode as $order) {
            $dir = substr($order, 0, 1) === '-' ? 'desc' : 'asc';
            $field = trim($order, " \t\n\r\0\x0B+-");
            $field = $this->getRealSortField($field);

            if (! $field) {
                continue;
            }

            $sorts[$field] = $dir;
        }

        if (empty($sorts)) {
            $sorts = $this->sortBy;
        }

        return $sorts;
    }

    protected function getRealSortField(string $field): ?string
    {
        $sortFields = $this->getSortFields();
        if (isset($sortFields[$field])) {
            return $sortFields[$field];
        }

        if (in_array($field, $sortFields)) {
            return $field;
        }

        return null;
    }
}
