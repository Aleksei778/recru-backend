<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Helpers\UrlHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\DomainRegisterRequest;
use App\Models\Tenant;
use App\Models\Tenant\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    /**
     * Display the register view.
     */
    public function index(): Response
    {
        return Inertia::render('Auth/Register', [
            'loginHref' => route('login.index'),
            'registerSubmit' => route('register.submit'),
        ]);
    }

    /**
     * Check domain
     */
    public function register(DomainRegisterRequest $request): RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        try {
            $validated = $request->validated();

            $domain = $validated['domain'];
            $email = $validated['email'];
            $password = $validated['password'];

            $tenant = Tenant::create();
            $tenantId =  $tenant->id;
            $tenant->domains()->create(['domain' => $domain]);

            $tenant->run(function () use ($email, $password, $tenantId) {
                Admin::create([
                    'name' => 'TenantAdmin_' . $tenantId,
                    'email' => $email,
                    'password' => Hash::make($password),
                ]);
            });

            return Inertia::location(UrlHelper::getTenantUrl($domain));

        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('flash.error', 'Произошла ошибка! Повторите пожалуйста.');
        }
    }
}