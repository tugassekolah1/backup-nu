<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Di balik proxy Vercel, paksa skema HTTPS agar URL yang di-generate memakai https.
        $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO);

        // DAFTARKAN ALIAS DI SINI
        $middleware->alias([
            'admin' => AdminMiddleware::class, // Ganti dengan class middleware admin Anda
        ]);

        // Tamu yang membuka halaman anggota diarahkan ke login anggota.
        $middleware->redirectGuestsTo(fn (Request $request) => $request->is('anggota', 'anggota/*')
            ? route('member.login')
            : route('login'));

        // Pengguna yang sudah login tidak perlu melihat halaman login lagi.
        // Hati-hati membedakan area anggota dan halaman login admin /login/admin.
        $middleware->redirectUsersTo(fn (Request $request) => $request->is('login', 'anggota', 'anggota/*')
            ? route('member.profile.edit')
            : '/dashboard');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (Throwable $e) {
            logger()->error($e::class.': '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return false;
        });
    })->create();
