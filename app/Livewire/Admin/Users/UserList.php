<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showAddModal = false;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'user';

    public $paginationTheme = 'bootstrap';

    protected function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', Rule::in(['user', 'staff'])],
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openAddModal(): void
    {
        $this->resetForm();
        $this->showAddModal = true;
    }

    public function closeAddModal(): void
    {
        $this->showAddModal = false;
        $this->resetForm();
    }

    public function addUser(): void
    {
        $this->validate();

        User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
            'role'     => $this->role,
        ]);

        $this->closeAddModal();
        $this->dispatch('toast', [
            'title'   => 'Success',
            'message' => 'User created successfully.',
            'icon'    => 'success',
        ]);
    }

    public function deleteUser(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            $this->dispatch('toast', [
                'title'   => 'Error',
                'message' => 'Admin users cannot be deleted.',
                'icon'    => 'error',
            ]);
            return;
        }

        if ($user->id === auth()->id()) {
            $this->dispatch('toast', [
                'title'   => 'Error',
                'message' => 'You cannot delete your own account.',
                'icon'    => 'error',
            ]);
            return;
        }

        $user->delete();

        $this->dispatch('toast', [
            'title'   => 'Success',
            'message' => 'User deleted successfully.',
            'icon'    => 'success',
        ]);
    }

    private function resetForm(): void
    {
        $this->name     = '';
        $this->email    = '';
        $this->password = '';
        $this->role     = 'user';
        $this->resetValidation();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'LIKE', '%' . $this->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.admin.users.user-list', compact('users'));
    }
}
