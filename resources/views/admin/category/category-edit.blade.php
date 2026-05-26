@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        {{-- Header --}}
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Edit Category</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('admin.categories') }}"><div class="text-tiny">Categories</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Edit</div></li>
            </ul>
        </div>

        <div class="wg-box">
            <form class="form-new-product form-style-1"
                  action="{{ route('admin.categories.update', $category->id) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('POST')
                <input type="hidden" name="id" value="{{ $category->id }}">

                {{-- Name --}}
                <fieldset class="name">
                    <div class="body-title">Category Name <span class="tf-color-1">*</span></div>
                    <input class="flex-grow @error('name') is-invalid @enderror"
                           type="text" name="name" placeholder="Category name"
                           value="{{ old('name', $category->name) }}" required autofocus>
                    @error('name')
                        <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                    @enderror
                </fieldset>

                {{-- Status --}}
                <fieldset class="name">
                    <div class="body-title">Status</div>
                    <div class="select flex-grow">
                        <select name="status">
                            <option value="1" {{ old('status', $category->is_active) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $category->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    @error('status')
                        <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                    @enderror
                </fieldset>

                {{-- Show on Home Page --}}
                <fieldset class="name">
                    <div class="body-title">Show on Home Page</div>
                    <div class="select flex-grow">
                        <select name="is_homepage_show">
                            <option value="0" {{ old('is_homepage_show', $category->is_homepage_show) == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('is_homepage_show', $category->is_homepage_show) == 1 ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                </fieldset>

                {{-- Show in Menu --}}
                <fieldset class="name">
                    <div class="body-title">Show in Menu</div>
                    <div class="select flex-grow">
                        <select name="is_show_in_menu">
                            <option value="0" {{ old('is_show_in_menu', $category->is_show_in_menu) == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('is_show_in_menu', $category->is_show_in_menu) == 1 ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                </fieldset>

                {{-- Display Order --}}
                <fieldset class="name">
                    <div class="body-title">Display Order</div>
                    <input class="flex-grow" type="number" name="display_order" min="0"
                           value="{{ old('display_order', $category->display_order) }}" placeholder="0">
                </fieldset>

                {{-- Parent Category --}}
                <fieldset class="name">
                    <div class="body-title">Parent Category</div>
                    <div class="select flex-grow">
                        <select name="parent_id">
                            <option value="">— None (root category) —</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                                @foreach ($cat->children as $child)
                                    <option value="{{ $child->id }}"
                                        {{ old('parent_id', $category->parent_id) == $child->id ? 'selected' : '' }}>
                                        &nbsp;&nbsp;&nbsp;↳ {{ $child->name }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                </fieldset>

                {{-- Description --}}
                <fieldset class="name">
                    <div class="body-title">Description</div>
                    <textarea class="flex-grow @error('description') is-invalid @enderror"
                              name="description" rows="4"
                              placeholder="Optional category description">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                    @enderror
                </fieldset>

                {{-- Image --}}
                <fieldset class="col-upload">
                    <div class="body-title">Category Image</div>

                    {{-- Current image preview --}}
                    @if ($category->image)
                        <div class="d-flex align-items-center gap-3 mb-2" id="current-img-preview">
                            <img src="{{ asset('images/category/' . $category->image) }}"
                                 alt="Current image"
                                 style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #e9ecef;">
                            <div>
                                <span class="text-muted d-block" style="font-size:13px;">Current image</span>
                                <button type="button" id="btn-remove-cat-img"
                                        class="btn btn-sm btn-outline-danger mt-1"
                                        data-url="{{ route('admin.categories.image.remove', $category->id) }}">
                                    <i class="icon-trash-2"></i> Remove
                                </button>
                            </div>
                        </div>
                    @endif

                    <input type="file" id="categoryImage" name="image" accept="image/*">
                    @error('image')
                        <span class="invalid-feedback d-block mt-1"><strong>{{ $message }}</strong></span>
                    @enderror
                </fieldset>

                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Save Changes</button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
createFilePond('categoryImage', { allowMultiple: false });

const btnRemove = document.getElementById('btn-remove-cat-img');
if (btnRemove) {
    btnRemove.addEventListener('click', function () {
        Swal.fire({
            title: 'Remove image?',
            text: 'This will permanently delete the category image.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, remove',
            cancelButtonText: 'Cancel',
        }).then(result => {
            if (!result.isConfirmed) return;

            fetch(btnRemove.dataset.url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('current-img-preview').remove();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Image removed',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true,
                    });
                }
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Failed to remove image', toast: true,
                    position: 'top-end', showConfirmButton: false, timer: 3000 });
            });
        });
    });
}
</script>
@endpush
