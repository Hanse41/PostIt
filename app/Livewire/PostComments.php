<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Comment;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
class PostComments extends Component
{
    public Post $post;
    public Collection $comments;
    public $newComment = '';
    public $replyTo = null;
    public $editingComment = null;
    public $perPage = 5;
    public $hasMorePages = false;
    public $expandedComments = [];

    public $repliesPerComment = 3;

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->loadComments();
    }
    public function loadMoreReplies($commentId)
    {
        if (!in_array($commentId, $this->expandedComments)) {
            $this->expandedComments[] = $commentId;
        }

        // Refresh the comment's replies
        $comment = $this->comments->find($commentId);
        if ($comment) {
            $currentCount = $this->expandedComments
                ? ($comment->replies->count() + 3)
                : 3;

            $comment->setRelation('replies',
                $comment->replies()
                       ->with('user')
                       ->take($currentCount)
                       ->get()
            );
        }
    }
    public function loadComments()
    {
        $this->comments = $this->post->comments()
            ->with(['user', 'replies' => function($query) {
                $query->with('user')
                      ->take($this->repliesPerComment)
                      ->latest();
            }])
            ->withCount('replies')
            ->whereNull('parent_id')
            ->latest()
            ->take($this->perPage)
            ->get();

        $this->hasMorePages = $this->post->comments()
            ->whereNull('parent_id')
            ->count() > $this->perPage;
    }

    public function addComment()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'newComment' => 'required|min:1'
        ]);

        $comment = $this->post->comments()->create([
            'user_id' => Auth::id(),
            'body' => $this->newComment,
            'parent_id' => $this->replyTo
        ]);

        $this->newComment = '';
        $this->replyTo = null;
        $this->loadComments();

        $this->dispatch('comment-added');
    }

    public function loadMore()
    {
        $this->perPage += 5;
        $this->loadComments();
    }

    #[On('setReplyTo')]
    public function setReplyTo($username, $commentId)
    {
        $this->replyTo = $commentId;
        $this->newComment = "@{$username} ";
    }

    #[On('startEditing')]
    public function startEditing($commentId)
    {
        $comment = Comment::find($commentId);
        if ($comment && $comment->user_id === Auth::id()) {
            $this->editingComment = $commentId;
            $this->newComment = $comment->body;
        }
    }

    public function updateComment()
    {
        $comment = Comment::find($this->editingComment);
        if ($comment && $comment->user_id === Auth::id()) {
            $this->validate([
                'newComment' => 'required|min:1'
            ]);

            $comment->update([
                'body' => $this->newComment
            ]);

            $this->editingComment = null;
            $this->newComment = '';
            $this->loadComments();
        }
    }

    #[On('deleteComment')]
    public function deleteComment($commentId)
    {
        $comment = Comment::find($commentId);
        if ($comment && ($comment->user_id === Auth::id() || $this->post->user_id === Auth::id())) {
            $comment->delete();
            $this->loadComments();
        }
    }

    public function cancelEdit()
    {
        $this->editingComment = null;
        $this->newComment = '';
        $this->replyTo = null;
    }

    public function render()
    {
        return view('livewire.post-comments');
    }
}
