<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Bisa log error global di sini jika mau
        });
    }

    public function render($request, Throwable $e)
    {
        // Jika request API / JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($e);
        }

        // Fallback ke default Laravel (HTML)
        return parent::render($request, $e);
    }

    /**
     * Tangani semua exception API secara global
     */
    protected function handleApiException(Throwable $exception): JsonResponse
    {
        $status = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
        $message = $exception->getMessage() ?: 'Server Error';
        $errors = null;

        if ($exception instanceof ValidationException) {
            $status = 422;
            $message = 'Validation failed';
            $errors = $exception->errors();
        } elseif ($exception instanceof AuthenticationException) {
            $status = 401;
            $message = 'Unauthenticated';
        } elseif ($exception instanceof NotFoundHttpException) {
            $status = 404;
            $message = 'Resource not found';
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $status = 405;
            $message = 'Method not allowed';
        }

        $response = [
            'status' => $status,
            'message' => $message,
        ];

        // Hanya sertakan trace jika APP_DEBUG=true
        if (config('app.debug')) {
            $response['trace'] = collect($exception->getTrace())
                ->map(function ($trace) {
                    return [
                        'file' => $trace['file'] ?? null,
                        'line' => $trace['line'] ?? null,
                        'function' => $trace['function'] ?? null,
                    ];
                })->all();
        }

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Standar JSON response untuk error API
     */
    protected function apiErrorResponse(string $message, int $status = 500, array $errors = null): JsonResponse
    {
        $response = [
            'status' => $status,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }
}
