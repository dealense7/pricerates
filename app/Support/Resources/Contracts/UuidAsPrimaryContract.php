<?php

declare(strict_types=1);

namespace App\Support\Resources\Contracts;

interface UuidAsPrimaryContract
{
    public function getKeyName();

    public function getUid(): string;

    public function getUidString(): string;

    public function isUuidBinary(string $value): bool;
}
