<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        $user->load(['posts' => function ($query){
            $query->latest()
                ->withCount([ 'likes', 'comments']);
        }]);

        return view('profile.profile_show', compact('user'));
    }
}
