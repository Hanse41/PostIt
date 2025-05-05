<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use FFMpeg\FFMpeg;
use Intervention\Image\Facades\Image;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'caption',
        'tags',
        'media_data'
    ];
    protected $casts = [
        'media_data' => 'array',
    ];
    protected $withCount = ['likes', 'comments'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function media()
    {
        return $this->hasMany(PostMedia::class);
    }



    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function isLikedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->likes()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bookmarks')
                    ->withTimestamps();
    }

    /**
     * Check if the post is bookmarked by a specific user
     */
    public function isBookmarkedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->bookmarks()->where('user_id', $user->id)->exists();
    }



}
