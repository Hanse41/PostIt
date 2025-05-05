<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WorkOSController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('workos-google')->redirect();
    }

    public function callback()
    {
        try {
            $workosUser = Socialite::driver('workos-google')->user();

            $user = User::updateOrCreate([
                'email' => $workosUser->email,
            ], [
                'name' => $workosUser->name,
                'password' => Hash::make(Str::random(24)),
                'avatar' => $workosUser->avatar,
                'avatar_source' => 'oauth'
            ]);

            Auth::login($user);

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Authentication failed. Please try again.'
            ]);
        }
    }
}
