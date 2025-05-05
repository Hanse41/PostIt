<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public ?string $bio = '';
    public ?string $address = '';
    public ?string $phone = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->bio = $user->bio;
        $this->address = $user->address;
        $this->phone = $user->phone;
    }

    /**
     * Save the account details for the currently authenticated user.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'bio' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:15'],
        ]);

        $user = Auth::user();
        $user->update($validated);

        $this->dispatch('account-details-updated');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Account Details')" :subheading="__('Update your bio, address, and phone number')">
        <form wire:submit="save" class="my-6 w-full space-y-6">
            <!-- Bio -->
            <flux:textarea wire:model="bio" :label="__('Bio')" required autofocus autocomplete="bio" />

            <!-- Address -->
            <div>
                <flux:input wire:model="address" :label="__('Address')" type="text" required autocomplete="address" />
            </div>

            <!-- Phone -->
            <div>
                <flux:input wire:model="phone" :label="__('Phone')" type="text" required autocomplete="phone" />
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="account-details-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>
