<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Livewire\Component;

class ContentTabs extends Component
{
    public User $user;
    public string $activeTab = 'posts';
    public string $sortBy = 'latest';

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function updatedSortBy()
    {
        $this->dispatch('sort-changed');
    }
    public function getPosts()
    {
        if ($this->activeTab === 'posts') {
            $query = $this->user->posts();
        } else {
            $query = $this->user->bookmarkedPosts()
                ->withPivot('created_at');
        }

        $query = match ($this->sortBy) {
            'oldest' => $this->activeTab === 'posts'
                ? $query->oldest()
                : $query->orderBy('bookmarks.created_at', 'asc'),
            'popular' => $query->withCount('likes')->orderByDesc('likes_count'),
            default => $this->activeTab === 'posts'
                ? $query->latest()
                : $query->orderBy('bookmarks.created_at', 'desc'),
        };

        return $query->withCount(['likes', 'comments'])->get();
    }

    public function render()
    {
        return view('livewire.profile.content-tabs', [
            'posts' => $this->getPosts()
        ]);
    }
}
