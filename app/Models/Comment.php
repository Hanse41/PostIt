<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'body', 'parent_id', 'mentioned_user'];

    protected $with = ['user', 'likes'];

    protected $appends = ['is_reply'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value),
        );
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'comment_likes')->withTimestamps();
    }

    public function isLikedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->likes()->where('user_id', $user->id)->exists();
    }
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function isReply(): bool
    {
        return $this->parent_id !== null;
    }

    public function getIsReplyAttribute(): bool
    {
        return $this->parent_id !== null;
    }

    public function getCreatedAtAttribute($value): string
    {
        return Carbon::parse($value)->diffForHumans();
    }

}
