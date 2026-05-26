<div>
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Users</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li>
                        <div class="text-tiny">Users</div>
                    </li>
                </ul>
            </div>

            <div class="wg-box">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="wg-filter">
                            <form class="form-search">
                                <fieldset class="name">
                                    <input wire:model.live="search" type="text" placeholder="Search by name or email..."
                                        class="" name="search" tabindex="2" aria-required="true">
                                </fieldset>
                                <div class="button-submit">
                                    <button class="" type="submit"><i class="icon-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end align-items-center">
                        <button wire:click="openAddModal" class="tf-button style-1">
                            <i class="icon-plus"></i> Add User
                        </button>
                    </div>
                </div>

                <div class="wg-table table-all-user">
                    <div class="divider"></div>
                    <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination mb-2">
                        {{ $users->links() }}
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:60px">#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th class="text-center">Role</th>
                                    <th class="text-center">Joined</th>
                                    <th class="text-center" style="width:100px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td class="text-center">
                                            @if ($user->role === 'admin')
                                                <span class="badge bg-danger">Admin</span>
                                            @elseif ($user->role === 'staff')
                                                <span class="badge bg-primary">Staff</span>
                                            @else
                                                <span class="badge bg-secondary">User</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $user->created_at->format('d M Y') }}</td>
                                        <td class="text-center">
                                            @if ($user->role !== 'admin')
                                                <button
                                                    wire:click="deleteUser({{ $user->id }})"
                                                    wire:confirm="Are you sure you want to delete this user?"
                                                    class="btn btn-sm btn-danger"
                                                    title="Delete user">
                                                    <i class="icon-trash"></i>
                                                </button>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Add User Modal --}}
    @if ($showAddModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New User</h5>
                        <button type="button" class="btn-close" wire:click="closeAddModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input wire:model="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                placeholder="Full name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input wire:model="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                placeholder="Email address">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input wire:model="password" type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Min 8 characters">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select wire:model="role" class="form-select @error('role') is-invalid @enderror">
                                <option value="user">User</option>
                                <option value="staff">Staff</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeAddModal">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="addUser">
                            <span wire:loading wire:target="addUser" class="spinner-border spinner-border-sm me-1"></span>
                            Create User
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
