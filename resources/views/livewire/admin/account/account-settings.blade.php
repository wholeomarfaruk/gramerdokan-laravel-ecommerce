<div>
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>My Account</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li>
                        <div class="text-tiny">Account</div>
                    </li>
                </ul>
            </div>

            <div class="row gap20">

                {{-- Profile Info Card --}}
                <div class="col-lg-8">
                    <div class="wg-box">
                        <h5 class="mb-4">Profile Information</h5>

                        {{-- Avatar Preview --}}
                        <div class="mb-4 text-center">
                            <div class="upload-image" style="justify-content: center;">
                                <div class="item" style="width:120px; height:120px; border-radius:50%; overflow:hidden; border:3px solid #e5e7eb; margin: 0 auto 12px;">
                                    @if ($photo)
                                        <img src="{{ $photo->temporaryUrl() }}" alt="Preview"
                                            style="width:100%; height:100%; object-fit:cover;">
                                    @else
                                        <img src="{{ $user->getAvatarUrl() }}" alt="Avatar"
                                            style="width:100%; height:100%; object-fit:cover;">
                                    @endif
                                </div>
                            </div>

                            <div class="upload-image" style="justify-content: center;">
                                <div class="item up-load" style="max-width:280px; margin: 0 auto;">
                                    <label class="uploadfile" for="avatarFile" style="padding: 10px 16px; cursor:pointer;">
                                        <span class="icon"><i class="icon-upload-cloud"></i></span>
                                        <span class="body-text">
                                            Click to upload avatar<br>
                                            <span class="text-tiny text-muted">JPG, JPEG, PNG, WEBP — max 2MB</span>
                                        </span>
                                        <input wire:model="photo" type="file" id="avatarFile"
                                            accept=".jpg,.jpeg,.png,.webp" style="display:none;">
                                    </label>
                                </div>
                            </div>
                            @error('photo')
                                <div class="text-danger mt-1 text-tiny">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label body-title">Full Name <span class="tf-color-1">*</span></label>
                            <input wire:model="name" type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Your full name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label body-title">Email Address <span class="tf-color-1">*</span></label>
                            <input wire:model="email" type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="Email address">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label class="form-label body-title">Role</label>
                            <input type="text" class="form-control bg-light" value="{{ ucfirst($user->role) }}" disabled>
                            <small class="text-muted">Role cannot be changed from here.</small>
                        </div>

                        <div class="mt-4">
                            <button wire:click="updateProfile" class="tf-button style-1"
                                wire:loading.attr="disabled" wire:target="updateProfile,photo">
                                <span wire:loading wire:target="updateProfile" class="spinner-border spinner-border-sm me-1"></span>
                                Save Profile
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Change Password Card --}}
                <div class="col-lg-4">
                    <div class="wg-box">
                        <h5 class="mb-4">Change Password</h5>

                        <div class="mb-3">
                            <label class="form-label body-title">Current Password <span class="tf-color-1">*</span></label>
                            <input wire:model="currentPassword" type="password"
                                class="form-control @error('currentPassword') is-invalid @enderror"
                                placeholder="Current password">
                            @error('currentPassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label body-title">New Password <span class="tf-color-1">*</span></label>
                            <input wire:model="newPassword" type="password"
                                class="form-control @error('newPassword') is-invalid @enderror"
                                placeholder="Min 8 characters">
                            @error('newPassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label body-title">Confirm New Password <span class="tf-color-1">*</span></label>
                            <input wire:model="newPasswordConfirmation" type="password"
                                class="form-control @error('newPasswordConfirmation') is-invalid @enderror"
                                placeholder="Repeat new password">
                            @error('newPasswordConfirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button wire:click="updatePassword" class="tf-button style-1 w-full"
                            wire:loading.attr="disabled" wire:target="updatePassword">
                            <span wire:loading wire:target="updatePassword" class="spinner-border spinner-border-sm me-1"></span>
                            Update Password
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
