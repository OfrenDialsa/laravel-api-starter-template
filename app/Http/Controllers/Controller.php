<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Laravel API Professional Starter Template",
 * description="
 * ### Overview
 * A high-performance, scalable API engine built with **Laravel**. This template is designed for long-term maintainability using industry best practices.
 * ### Architecture Features
 * **Modular Architecture**: Code is organized by domain modules (Auth, User, etc.) to prevent 'Spaghetti Code'.
 * **Clean Architecture Principles**: Strict separation of concerns between Controllers, Services, and Repositories.
 * **Command-Line Module Generator**: Quickly scaffold new modules with built-in artisan commands.
 * **TDD Ready**: Built with a 'Test-First' mindset using PHPUnit and Pest for high code reliability.
 * ### Security
 * **Sanctum Authentication**: Secure token-based access.
 * **Signed URLs**: Enhanced security for email verification.
 * ### How to use
 * 1. Authorize using the `apiAuth` bearer token.
 * 2. Explore endpoints grouped by module tags below.",
 * @OA\Contact(
 * email="ofrendialsa.dev@gmail.com"
 * ),
 * @OA\License(
 * name="Apache 2.0",
 * url="http://www.apache.org/licenses/LICENSE-2.0.html"
 * )
 * )
 *
 * @OA\Server(
 * url=L5_SWAGGER_CONST_HOST
 * )
 *
 * @OA\SecurityScheme(
 * type="http",
 * description="Enter your Bearer token to access protected endpoints",
 * name="Authorization",
 * in="header",
 * scheme="bearer",
 * bearerFormat="JWT",
 * securityScheme="apiAuth",
 * )
 */
abstract class Controller
{
    //
}