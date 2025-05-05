<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $posts = Post::with([ 'user'])
            ->latest()
            ->paginate(10);

        return view('dashboard', compact('posts'));
    }

}
