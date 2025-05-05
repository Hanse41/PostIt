<x-layouts.app :title="__('Edit Post')">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Post</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Update your post content and media</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm p-6">
            <form action="{{ route('posts.update', $post) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  x-data="{
                    preview: [],
                    maxFiles: 10,
                    deletedMedia: [],
                    handleFiles(event) {
                        const files = Array.from(event.target.files);

                        // Count current media (excluding marked for deletion)
                        const currentMediaCount = {{ $post->media->count() }} - this.deletedMedia.length;

                        // Count current videos (excluding marked for deletion)
                        const currentVideos = {{ $post->media->where('media_type', 'video')->count() }} -
                            this.deletedMedia.filter(id =>
                                @json($post->media->where('media_type', 'video')->pluck('id')).includes(id)
                            ).length;

                        // Process new files
                        files.forEach(file => {
                            const isVideo = file.type.startsWith('video/');

                            // Check video limit
                            if (isVideo && currentVideos > 0) {
                                alert('You can only have one video per post.');
                                return;
                            }

                            // Check total files limit
                            if (this.preview.length + currentMediaCount >= this.maxFiles) {
                                alert(`You can only have up to ${this.maxFiles} files total.`);
                                return;
                            }

                            // Check file size
                            if (file.size > 20 * 1024 * 1024) {
                                alert(`File ${file.name} is too large. Maximum size is 20MB.`);
                                return;
                            }

                            const url = URL.createObjectURL(file);
                            this.preview.push({
                                type: isVideo ? 'video' : 'image',
                                url: url,
                                name: file.name
                            });
                        });
                    },
                    removeFile(index) {
                        URL.revokeObjectURL(this.preview[index].url);
                        this.preview.splice(index, 1);
                    }
                }">
                @csrf
                @method('PUT')

                <!-- Current Media -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Media</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($post->media as $media)
                            <div class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-zinc-800"
                                x-data="{ markedForDeletion: false }"
                                :class="{ 'ring-2 ring-red-500 ring-offset-2': markedForDeletion }">
                                <!-- Deletion Overlay -->
                                <div class="absolute inset-0 bg-black/60 z-10 flex items-center justify-center transition-opacity duration-200"
                                    :class="{ 'opacity-100': markedForDeletion, 'opacity-0 pointer-events-none': !markedForDeletion }">
                                    <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>

                                @if($media->media_type === 'video')
                                    <video class="w-full h-full object-cover" controls>
                                        <source src="{{ Storage::url($media->media_path) }}" type="video/mp4">
                                    </video>
                                @else
                                    <img src="{{ Storage::url($media->media_path) }}"
                                        alt="Post media"
                                        class="w-full h-full object-cover"
                                        :class="{ 'opacity-50': markedForDeletion }">
                                @endif

                                <!-- Delete Button -->
                                <button type="button"
                                        @click="markedForDeletion = !markedForDeletion;
                                                if(markedForDeletion) {
                                                    deletedMedia.push({{ $media->id }})
                                                } else {
                                                    deletedMedia = deletedMedia.filter(id => id !== {{ $media->id }})
                                                }"
                                        :class="{ 'bg-red-500 hover:bg-red-600': markedForDeletion, 'bg-black/50 hover:bg-black/75': !markedForDeletion }"
                                        class="absolute top-2 right-2 p-2 text-white rounded-full z-20 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                <input type="hidden"
                                    :name="markedForDeletion ? 'deleted_media[]' : ''"
                                    :value="markedForDeletion ? {{ $media->id }} : ''">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Add New Media -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Add New Media</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col w-full h-40 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200">
                            <div class="flex flex-col items-center justify-center pt-7">
                                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Click here to upload your files</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Support: JPG, JPEG, PNG, GIF, MP4</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Up to 10 files (20MB each)</p>
                            </div>
                            <input type="file"
                                   name="media[]"
                                   multiple
                                   accept="image/*,video/*"
                                   class="hidden"
                                   @change="handleFiles($event)">
                        </label>
                    </div>
                    @error('media.*')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Media Preview -->
                <div class="mb-8 grid grid-cols-2 md:grid-cols-4 gap-4" x-show="preview.length">
                    <template x-for="(item, index) in preview" :key="index">
                        <div class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-zinc-800">
                            <template x-if="item.type === 'image'">
                                <img :src="item.url" class="w-full h-full object-cover">
                            </template>
                            <template x-if="item.type === 'video'">
                                <video class="w-full h-full object-cover" controls>
                                    <source :src="item.url" type="video/mp4">
                                </video>
                            </template>
                            <button type="button"
                                    @click="removeFile(index)"
                                    class="absolute top-2 right-2 p-2 bg-black/50 text-white rounded-full hover:bg-black/75 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Caption -->
                <div class="mb-8">
                    <label for="caption" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Caption</label>
                    <textarea id="caption"
                             name="caption"
                             rows="3"
                             class="mt-1 p-3 block w-full rounded-lg border-gray-300 dark:border-gray-700 shadow-sm focus:border-pink-500 focus:ring-pink-500 dark:bg-zinc-800 dark:text-gray-200 transition-colors duration-200"
                             placeholder="What's on your mind?">{{ $post->caption }}</textarea>
                    @error('caption')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tags -->
                <div class="mb-8" x-data="{ tags: {{ $post->tags ? json_encode(json_decode($post->tags)) : '[\'\']' }} }">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tags</label>
                    <div class="space-y-3">
                        <template x-for="(tag, index) in tags" :key="index">
                            <div class="flex items-center space-x-2">
                                <div class="flex-1 relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">#</span>
                                    <input type="text"
                                           :name="'tags[' + index + ']'"
                                           x-model="tags[index]"
                                           class="block w-full pl-8 p-2 rounded-lg border-gray-300 dark:border-gray-700 shadow-sm focus:border-pink-500 focus:ring-pink-500 dark:bg-zinc-800 dark:text-gray-200"
                                           placeholder="Enter a tag">
                                </div>
                                <button type="button"
                                        @click="tags.length > 1 && tags.splice(index, 1)"
                                        class="p-2 text-gray-400 hover:text-red-500 transition-colors duration-200"
                                        x-show="tags.length > 1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <button type="button"
                            @click="tags.push('')"
                            class="mt-3 inline-flex items-center px-4 py-2 text-sm font-medium text-pink-600 dark:text-pink-500 hover:text-pink-700 dark:hover:text-pink-400 focus:outline-none transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add another tag
                    </button>
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('posts.show', $post) }}"
                       class="px-6 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-200 transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-pink-500 to-orange-500 text-white text-sm font-medium rounded-lg hover:from-pink-600 hover:to-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-all duration-200 shadow-sm">
                        Update Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
