<?php

namespace App\Http\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AccountDetails extends Component
{
    public $bio;
    public $address;
    public $phone;

    public function mount()
    {
        $user = Auth::user();
        $this->bio = $user->bio;
        $this->address = $user->address;
        $this->phone = $user->phone;
    }

    public function save()
    {
        $this->validate([
            'bio' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
        ]);

        $user = Auth::user();
        $user->update([
            'bio' => $this->bio,
            'address' => $this->address,
            'phone' => $this->phone,
        ]);

        session()->flash('success', 'Account details updated successfully!');
    }

    public function render()
    {
        return view('livewire.settings.account-details');
    }
}
