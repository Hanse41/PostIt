<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Comment;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CommentSection extends Component
{
    public Post $post;
    public string $newComment = '';
    public bool $showAllComments = false;

    public function mount(Post $post)
    {
        $this->post = $post;
    }

    public function addComment()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'newComment' => 'required|min:1'
        ]);

        $this->post->comments()->create([
            'user_id' => Auth::id(),
            'body' => $this->newComment
        ]);

        $this->newComment = '';
        $this->post->refresh();
    }

    public function toggleComments()
    {
        $this->showAllComments = !$this->showAllComments;
    }

    public function deleteComment(Comment $comment)
    {
        if (Auth::id() === $comment->user_id || Auth::id() === $this->post->user_id) {
            $comment->delete();
            $this->post->refresh();
        }
    }

    public function render()
    {
        $comments = $this->showAllComments
            ? $this->post->comments()->with('user')->latest()->get()
            : $this->post->comments()->with('user')->latest()->take(2)->get();

        return view('livewire.comment-section', [
            'comments' => $comments
        ]);
    }
}
