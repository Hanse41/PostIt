<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Comment;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CommentModal extends Component
{
    public Post $post;
    public string $newComment = '';
    public bool $showEmojiPicker = false;
    public ?string $replyToUsername = null;
    public ?int $parentCommentId = null;
    public ?string $editingComment = null;
    public ?int $editingCommentId = null;
    public int $commentsCount = 0;
    public $repliesCount = 3;
    public $commentRepliesCounts = [];
    public $initialRepliesShown = 3;

    protected $listeners = [
        'openComments' => 'open',
        'commentAdded' => '$refresh',
        'commentUpdated' => '$refresh',
        'commentDeleted' => '$refresh',
    ];

    public function open()
    {
        $this->dispatch('openModal');
    }

    public function getTotalCommentsCount()
    {
        // Get main comments count
        $mainCommentsCount = $this->post->comments()
            ->whereNull('parent_id')
            ->count();

        // Get replies count
        $repliesCount = $this->post->comments()
            ->whereNotNull('parent_id')
            ->count();

        return $mainCommentsCount + $repliesCount;
    }


    // Add this to handle updates
    protected function getListeners()
    {
        return [
            'openComments' => 'open',
            'commentAdded' => 'refreshCommentCount',
            'commentUpdated' => 'refreshCommentCount',
            'commentDeleted' => 'refreshCommentCount',
        ];
    }

    public function refreshCommentCount()
    {
        $this->commentsCount = $this->getTotalCommentsCount();
    }

    public function mount(Post $post)
        {
            $this->post = $post;
            $this->commentsCount = $this->getTotalCommentsCount();

            // Initialize reply counts
            $comments = $this->post->comments()->whereNull('parent_id')->get();
            foreach ($comments as $comment) {
                $this->commentRepliesCounts[$comment->id] = $this->initialRepliesShown;
            }
    }

    public function loadMoreReplies($commentId)
        {
            // Initialize if not set
            if (!isset($this->commentRepliesCounts[$commentId])) {
                $this->commentRepliesCounts[$commentId] = $this->repliesCount;
            }

            // Get total replies for this comment
            $comment = Comment::find($commentId);
            $totalReplies = $comment->replies()->count();

            // Increment by repliesCount, but don't exceed total
            $newCount = min(
                $totalReplies,
                $this->commentRepliesCounts[$commentId] + $this->repliesCount
            );

            $this->commentRepliesCounts[$commentId] = $newCount;
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
        $this->dispatch('commentAdded');
    }

    public function toggleCommentLike(Comment $comment)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($comment->isLikedBy($user)) {
            $comment->likes()->detach($user->id);
        } else {
            $comment->likes()->attach($user->id);
        }

        $this->dispatch('commentLiked');
    }

    public function setReplyTo($username, $commentId = null)
    {
        $comment = Comment::find($commentId);

        if ($comment) {
            if ($comment->isReply()) {
                // If replying to a reply, just store the username
                $this->replyToUsername = $username;
                $this->parentCommentId = $comment->parent_id;
            } else {
                // If replying to main comment, set parent_id and username
                $this->replyToUsername = $username;
                $this->parentCommentId = $comment->id;
            }
        }

        $this->dispatch('openModal');
    }

    public function cancelReply()
    {
        $this->reset(['replyToUsername', 'parentCommentId', 'newComment']);
    }


    public function startEditing(Comment $comment)
    {
        if (!$comment->user_id === auth()->id()) {
            return;
        }

        $this->editingComment = $comment->body;
        $this->editingCommentId = $comment->id;
    }

    public function updateComment()
    {
        if (!$this->editingCommentId) {
            return;
        }

        $comment = Comment::find($this->editingCommentId);

        if (!$comment || $comment->user_id !== auth()->id()) {
            return;
        }

        $this->validate([
            'editingComment' => 'required|min:1'
        ]);

        $comment->update([
            'body' => $this->editingComment
        ]);

        $this->cancelEditing();
        $this->dispatch('commentUpdated');
    }

    public function cancelEditing()
    {
        $this->reset(['editingComment', 'editingCommentId']);
    }

    public function deleteComment(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            return;
        }

        $comment->delete();
        $this->dispatch('commentDeleted');
    }


    public function render()
    {
        $comments = $this->post->comments()
            ->whereNull('parent_id')
            ->with(['user', 'likes', 'replies.user', 'replies.likes'])
            ->latest()
            ->get()
            ->map(function ($comment) {
                $comment->totalRepliesCount = $comment->replies()->count();
                $showCount = $this->commentRepliesCounts[$comment->id] ?? $this->repliesCount;

                // Get replies oldest first
                $replies = $comment->replies()
                    ->with(['user', 'likes'])
                    ->oldest()  // Changed to oldest() to show oldest first
                    ->take($showCount)
                    ->get();

                $comment->setRelation('replies', $replies);
                $comment->showRepliesCount = $showCount;

                return $comment;
            });

        return view('livewire.comment-modal', [
            'comments' => $comments
        ]);
    }
}
