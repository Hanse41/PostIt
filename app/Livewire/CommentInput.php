<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CommentInput extends Component
{
    public Post $post;
    public string $newComment = '';
    public ?string $replyToUsername = null;
    public ?int $parentCommentId = null;

    protected $listeners = ['setReplyTo'];

    public function mount(Post $post)
    {
        $this->post = $post;
    }

    public function addEmoji($emoji)
    {
        $this->newComment = ($this->newComment ?? '') . $emoji;
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
            'body' => $this->newComment,
            'parent_id' => $this->parentCommentId,
            'mentioned_user' => $this->replyToUsername,
        ]);

        $this->reset(['newComment', 'replyToUsername', 'parentCommentId']);
        $this->dispatch('comment-added');
    }

    public function setReplyTo($data)
    {
        $this->replyToUsername = $data['username'];
        $this->parentCommentId = isset($data['parentId']) ? $data['parentId'] : $data['commentId'];
    }

    public function cancelReply()
    {
        $this->reset(['replyToUsername', 'parentCommentId', 'newComment']);
    }

    public function render()
    {
        return view('livewire.comment-input');
    }
}
