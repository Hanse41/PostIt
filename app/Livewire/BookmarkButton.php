<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class BookmarkButton extends Component
{
    public Post $post;
    public bool $isBookmarked = false;

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->isBookmarked = Auth::check() && $this->post->bookmarks()->where('user_id', Auth::id())->exists();
    }

    public function toggleBookmark()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if ($this->isBookmarked) {
            $this->post->bookmarks()->detach(Auth::id());
        } else {
            $this->post->bookmarks()->attach(Auth::id());
        }

        $this->isBookmarked = !$this->isBookmarked;

        $this->dispatch('bookmark-updated');
    }

    public function render()
    {
        return view('livewire.bookmark-button');
    }
}
