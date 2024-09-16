<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function loginAsUser(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'Nie możesz zalogować się na swoje konto.');
        }

        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Zalogowano jako ' . $user->name);
    }
}
