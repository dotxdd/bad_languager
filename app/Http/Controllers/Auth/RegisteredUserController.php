<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
//        $schemaName = 'tenant_' . $user->id;
//
//        // Tworzenie nowego schematu
//        DB::statement("CREATE SCHEMA `$schemaName`");
//
//        // Ustawienie połączenia z nowym schematem
//        config(['database.connections.tenant.database' => $schemaName]);
//        DB::purge('tenant');
//        DB::reconnect('tenant');
//        DB::setDefaultConnection('tenant');
//
//        // Uruchamianie migracji dla nowego schematu
//        Artisan::call('migrate', [
//            '--database' => 'tenant',
//            '--path' => 'database/migrations/tenant',
//            '--force' => true,
//        ]);

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
