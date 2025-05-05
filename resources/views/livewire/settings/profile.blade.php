<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;

new class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $avatar;
    public $newAvatar;

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->avatar = Auth::user()->avatar;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'newAvatar' => $this->newAvatar ? ['image', 'max:2048'] : '',
        ]);

        if ($this->newAvatar) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $avatarPath = $this->newAvatar->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->name = $validated['name'];
        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function deleteAvatar(): void
    {
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = null;
            $user->save();
            $this->avatar = null;
        }

        $this->dispatch('profile-updated');
    }
}; ?>

<section class="w-full ">
    @include('partials.settings-heading')


    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your profile information')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <!-- Avatar Section -->


            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required disabled autocomplete="email" />
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>

        </form>

    </x-settings.layout>

</section>






