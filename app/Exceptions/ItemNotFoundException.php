<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

use function __;
use function response;

class ItemNotFoundException extends Exception implements Responsable
{
    public function __construct($message = '', $code = 404, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = __('app.item_not_found');
        }

        parent::__construct($message, $code, $previous);
    }

    public function toResponse($request): Response
    {
        return $request->wantsJson()
            ? response()->json(['message' => $this->getMessage()], $this->getCode())
            : redirect()->back()->withErrors($this->getMessage());
    }
}
