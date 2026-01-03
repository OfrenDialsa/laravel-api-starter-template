<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Laravel API Starter Template Documentation",
 * description="Centralized documentation for all API endpoints",
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