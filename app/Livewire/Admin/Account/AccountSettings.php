<?php

namespace App\Livewire\Admin\Account;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountSettings extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $currentPassword = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';
    public $photo = null;

    public function mount(): void
    {
        $user = auth()->user();
        $this->name  = $user->name;
        $this->email = $user->email;
    }

    protected function profileRules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore(auth()->id())],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    protected function passwordRules(): array
    {
        return [
            'currentPassword'          => ['required'],
            'newPassword'              => ['required', 'string', 'min:8', 'same:newPasswordConfirmation'],
            'newPasswordConfirmation'  => ['required'],
        ];
    }

    public function updateProfile(): void
    {
        $this->validate($this->profileRules());

        $user = auth()->user();
        $data = [
            'name'  => $this->name,
            'email' => $this->email,
        ];

        if ($this->photo) {
            $path = public_path('storage/images/user/');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            // Delete old avatar if it exists
            if ($user->avatar && file_exists($path . $user->avatar)) {
                unlink($path . $user->avatar);
            }

            $imageName = time() . '.' . $this->photo->getClientOriginalExtension();
            $this->photo->move($path, $imageName);
            $data['avatar'] = $imageName;
            $this->photo = null;
        }

        $user->update($data);

        $this->dispatch('toast', [
            'title'   => 'Success',
            'message' => 'Profile updated successfully.',
            'icon'    => 'success',
        ]);
    }

    public function updatePassword(): void
    {
        $this->validate($this->passwordRules());

        $user = auth()->user();

        if (!Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'The current password is incorrect.');
            return;
        }

        $user->update(['password' => Hash::make($this->newPassword)]);

        $this->currentPassword         = '';
        $this->newPassword             = '';
        $this->newPasswordConfirmation = '';
        $this->resetValidation(['currentPassword', 'newPassword', 'newPasswordConfirmation']);

        $this->dispatch('toast', [
            'title'   => 'Success',
            'message' => 'Password updated successfully.',
            'icon'    => 'success',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.account.account-settings', [
            'user' => auth()->user(),
        ]);
    }
}
