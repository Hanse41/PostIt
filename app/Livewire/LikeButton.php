<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LikeButton extends Component
{
    public Post $post;
    public bool $isLiked = false;  // Set explicit default
    public int $likesCount = 0;    // Set explicit default

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->isLiked = Auth::check() ? $post->isLikedBy(Auth::user()) : false;
        $this->likesCount = $post->likes()->count();
    }

    public function toggleLike()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if ($this->isLiked) {
            $this->post->likes()->where('user_id', auth()->id())->delete();
            $this->likesCount--;
        } else {
            $this->post->likes()->create(['user_id' => auth()->id()]);
            $this->likesCount++;
        }

        $this->isLiked = !$this->isLiked;
    }

    public function render()
    {
        return view('livewire.like-button');
    }
}
