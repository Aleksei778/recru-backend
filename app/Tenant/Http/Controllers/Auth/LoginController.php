<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Helpers\UrlHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function index(): Response
    {
        return Inertia::render('Auth/Login', [
            'registerHref' => route('register.index'),
            'loginSubmit' => route('login.submit'),
        ]);
    }

    /**
     * Login (check domain)
     */
    public function login(LoginRequest $request): \Symfony\Component\HttpFoundation\Response
    {
        try {
            $validated = $request->validated();
            Log::info('Login method called', ['request_data' => $request->all()]);

            return Inertia::location(UrlHelper::getTenantUrl($validated['domain']));
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('flash.error', 'Произошла ошибка! Повторите пожалуйста.');
        }
    }
}
