<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (HttpException $exception, Request $request) {
            $statusCode = $exception->getStatusCode();

            /* if ($statusCode == 400) {
                return response()->view("errors.400", [], 400);
            }

            if ($statusCode == 403) {
                return response()->view("errors.403", [], 403);
            } */

            if ($statusCode == 404) {
                return response()->view("errors.404", [], 404);
            }

            /* if ($statusCode == 500) {
                return response()->view("errors.500", [], 500);
            }

            if ($statusCode == 503) {
                return response()->view("errors.503", [], 503);
            } */

            //return response()->view('errors.default', [], $statusCode);
            return response()->view('errors.404', [], $statusCode);
        });
    })->create();
