<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use App\Models\PostMedia;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'comments.user', 'media']) // Eager-load media relationship
            ->withCount(['likes', 'comments']);

        // Apply sorting
        switch ($request->sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'my_posts':
                $query->where('user_id', auth()->id());
                break;
            default:
                $query->latest();
        }

        $posts = $query->paginate(10)->withQueryString();

        return view('posts.index', compact('posts'));
    }

    public function show(Post $post)
    {
        $post->load(['user', 'comments.user', 'media']) // Add media to eager loading
             ->loadCount(['likes', 'comments']);

        return view('posts.show', [
            'post' => $post,
            'posts' => collect([$post])
        ]);
    }

    public function create()
    {
        return view('posts.create');

    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }


    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'media.*' => 'required|file|mimes:jpeg,png,jpg,gif,mp4|max:20480', // Allow images and videos
            'caption' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        // Separate videos and images
        $videos = [];
        $images = [];

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                if (Str::startsWith($file->getMimeType(), 'video/')) {
                    $videos[] = $file;
                } else {
                    $images[] = $file;
                }
            }
        }

        // Ensure only one video is uploaded
        if (count($videos) > 1) {
            return back()->withErrors(['media' => 'You can only upload one video per post.'])->withInput();
        }

        // Create the post
        $post = Post::create([
            'user_id' => Auth::id(),
            'caption' => $validated['caption'] ?? null,
            'tags' => json_encode(array_filter(array_map(fn($tag) => ltrim($tag, '#'), $validated['tags'] ?? []))),
        ]);

        // Handle video upload
        if (!empty($videos)) {
            $video = $videos[0];
            $filename = time() . '_' . uniqid() . '.' . $video->getClientOriginalExtension();
            $path = $video->storeAs('uploads/media', $filename, 'public');

            // Save video record
            $post->media()->create([
                'media_path' => $path,
                'media_type' => 'video',
            ]);
        }

        // Handle image uploads
        foreach ($images as $image) {
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/media', $filename, 'public');

            // Save image record
            $post->media()->create([
                'media_path' => $path,
                'media_type' => 'image',
            ]);
        }

        return redirect()->route('posts.index')->with('success', 'Post created successfully!');
    }

    public function update(Request $request, Post $post)
    {
        // Validate the request
        $validated = $request->validate([
            'media.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:20480',
            'caption' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'deleted_media' => 'nullable|array',
            'deleted_media.*' => 'exists:post_media,id'
        ]);

        // Handle deleted media
        if ($request->has('deleted_media')) {
            foreach ($request->deleted_media as $mediaId) {
                $media = $post->media()->find($mediaId);
                if ($media) {
                    Storage::disk('public')->delete($media->media_path);
                    $media->delete();
                }
            }
        }

        // Handle new media uploads
        if ($request->hasFile('media')) {
            $videos = [];
            $images = [];

            foreach ($request->file('media') as $file) {
                if (Str::startsWith($file->getMimeType(), 'video/')) {
                    $videos[] = $file;
                } else {
                    $images[] = $file;
                }
            }

            // Check video constraints
            $existingVideos = $post->media()->where('media_type', 'video')->count();
            if (count($videos) + $existingVideos > 1) {
                return back()->withErrors(['media' => 'You can only have one video per post.'])->withInput();
            }

            // Process new media
            foreach ($videos as $video) {
                $filename = time() . '_' . uniqid() . '.' . $video->getClientOriginalExtension();
                $path = $video->storeAs('uploads/media', $filename, 'public');

                $post->media()->create([
                    'media_path' => $path,
                    'media_type' => 'video',
                ]);
            }

            foreach ($images as $image) {
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('uploads/media', $filename, 'public');

                $post->media()->create([
                    'media_path' => $path,
                    'media_type' => 'image',
                ]);
            }
        }

        // Update post details
        $post->update([
            'caption' => $validated['caption'] ?? null,
            'tags' => json_encode(array_filter(array_map(fn($tag) => ltrim($tag, '#'), $validated['tags'] ?? []))),
        ]);

        return redirect()->route('posts.show', $post)->with('success', 'Post updated successfully!');
    }
    public function destroy(Post $post)
        {
            // Delete the post and its media
            $media = json_decode($post->media, true);
            if ($media) {
                foreach ($media as $path) {
                    Storage::disk('public')->delete($path);
                }
            }

            $post->delete();

            return redirect()->route('posts.index')->with('success', 'Post deleted successfully!');
    }

    private function handleMediaUploads($files, Post $post)
        {
            $uploadPath = "uploads/posts/{$post->id}";

            // Ensure upload directory exists
            if (!Storage::disk('public')->exists($uploadPath)) {
                Storage::disk('public')->makeDirectory($uploadPath);
            }

            foreach ($files as $index => $file) {
                $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $mediaType = $this->getMediaType($file);

                if ($mediaType === 'image') {
                    // Process image
                    $image = Image::make($file);

                    // Resize if larger than 2000px
                    if ($image->width() > 2000) {
                        $image->resize(2000, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }

                    // Save optimized image
                    $image->save(storage_path("app/public/{$uploadPath}/{$fileName}"), 80);
                } else {
                    // Store video directly
                    $file->storeAs($uploadPath, $fileName, 'public');
                }

                // Create media record
                PostMedia::create([
                    'post_id' => $post->id,
                    'media_path' => "{$uploadPath}/{$fileName}",
                    'media_type' => $mediaType,
                    'order' => $index
                ]);
            }
    }

    private function getMediaType($file)
        {
            return Str::startsWith($file->getMimeType(), 'video/') ? 'video' : 'image';
    }

    private function determinePostType($media)
        {
            if (is_array($media) || is_object($media)) {
                $hasVideo = false;
                $hasImage = false;

                foreach ($media as $item) {
                    $mimeType = is_object($item) ? $item->getMimeType() : $item->media_type;
                    if (Str::startsWith($mimeType, 'video/')) {
                        $hasVideo = true;
                    } else {
                        $hasImage = true;
                    }
                }

                if ($hasVideo && $hasImage) return 'mixed';
                if ($hasVideo) return 'video';
                return 'image';
            }

            return 'image';
    }
}

