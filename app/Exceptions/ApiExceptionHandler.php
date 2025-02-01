<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Support\Helpers\Helper;
use Illuminate\Http\JsonResponse;
use Throwable;

class ApiExceptionHandler extends Handler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request   $request
     * @param \Throwable $th
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, Throwable $th): JsonResponse
    {
        try {
            return $this->renderApi($th, $th->getCode());
        } catch (Throwable $e) {
            return $this->renderApi($th, 400);
        }
    }

    protected function renderApi(Throwable $th, ?int $code = null): JsonResponse
    {
        return Helper::response(false, [], $code, [
            'message' => [app()->environment('production') ? 'something_went_wrong' : $th->getMessage()],
        ]);
    }
}
