@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        {{-- Header --}}
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>New Category</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('admin.categories') }}"><div class="text-tiny">Categories</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">New Category</div></li>
            </ul>
        </div>

        <div class="wg-box">
            <form class="form-new-product form-style-1"
                  action="{{ route('admin.categories.store') }}"
                  method="POST">
                @csrf

                {{-- Name --}}
                <fieldset class="name">
                    <div class="body-title">Category Name <span class="tf-color-1">*</span></div>
                    <input class="flex-grow @error('name') is-invalid @enderror"
                           type="text" name="name" placeholder="Category name"
                           value="{{ old('name') }}" required autofocus
                           oninput="autoSlug(this.value)">
                    @error('name')
                        <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                    @enderror
                </fieldset>

                {{-- Slug --}}
                <fieldset class="name">
                    <div class="body-title">Slug</div>
                    <input class="flex-grow" type="text" id="slug_input" name="slug"
                           placeholder="auto-generated from name"
                           value="{{ old('slug') }}">
                </fieldset>

                {{-- Status --}}
                <fieldset class="name">
                    <div class="body-title">Status</div>
                    <div class="select flex-grow">
                        <select name="is_active">
                            <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active', 1) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </fieldset>

                {{-- Show on Home Page --}}
                <fieldset class="name">
                    <div class="body-title">Show on Home Page</div>
                    <div class="select flex-grow">
                        <select name="is_homepage_show">
                            <option value="0" {{ old('is_homepage_show', 0) == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('is_homepage_show', 0) == 1 ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                </fieldset>

                {{-- Homepage Category Box --}}
                <fieldset class="name">
                    <div class="body-title">Show as Homepage Category Box</div>
                    <div class="select flex-grow">
                        <select name="homepage_category">
                            <option value="0" {{ old('homepage_category', 0) == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('homepage_category', 0) == 1 ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                </fieldset>

                {{-- Show in Menu --}}
                <fieldset class="name">
                    <div class="body-title">Show in Menu</div>
                    <div class="select flex-grow">
                        <select name="is_show_in_menu">
                            <option value="0" {{ old('is_show_in_menu', 0) == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('is_show_in_menu', 0) == 1 ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                </fieldset>

                {{-- Display Order --}}
                <fieldset class="name">
                    <div class="body-title">Display Order</div>
                    <input class="flex-grow" type="number" name="display_order" min="0"
                           value="{{ old('display_order', 0) }}" placeholder="0">
                </fieldset>

                {{-- Parent Category --}}
                <fieldset class="name">
                    <div class="body-title">Parent Category</div>
                    <div class="select flex-grow">
                        <select name="parent_id">
                            <option value="">— None (root category) —</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('parent_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                                @foreach ($cat->children as $child)
                                    <option value="{{ $child->id }}"
                                        {{ old('parent_id') == $child->id ? 'selected' : '' }}>
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
                              placeholder="Optional category description">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                    @enderror
                </fieldset>

                {{-- Category Image --}}
                <fieldset class="col-upload">
                    <div class="body-title">Category Image</div>
                    <input type="hidden" name="image" id="cat_image_url" value="{{ old('image') }}">
                    @error('image')
                        <span class="invalid-feedback d-block mb-2"><strong>{{ $message }}</strong></span>
                    @enderror
                    <div id="cat_image_preview" style="{{ old('image') ? '' : 'display:none;' }} margin-bottom:10px;">
                        <img id="cat_image_preview_img" src="{{ old('image') }}"
                             style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;display:block;margin-bottom:6px;">
                        <button type="button" id="cat_image_remove" class="tf-button style-1" style="font-size:12px;padding:4px 12px;">
                            <i class="icon-x"></i> Remove
                        </button>
                    </div>
                    <button type="button" id="cat_image_pick" class="tf-button style-1"
                            style="{{ old('image') ? 'display:none;' : '' }}"
                            onclick="Livewire.dispatch('open-media-picker', { multiple: false, callbackKey: 'cat_image' })">
                        <i class="icon-image"></i> Choose from Media Library
                    </button>
                </fieldset>

                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Save</button>
                </div>
            </form>
        </div>

    </div>
</div>

@livewire('admin.media.media-picker')
@endsection

@push('scripts')
<script>
function autoSlug(val) {
    const slug = val.trim().toLowerCase()
        .replace(/[^a-z0-9 -]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
    document.getElementById('slug_input').value = slug;
}

window.addEventListener('media-picker-confirmed', e => {
    const payload = e.detail[0] ?? e.detail;
    if (payload.callbackKey !== 'cat_image') return;
    const single = payload.single;
    if (!single) return;
    document.getElementById('cat_image_url').value          = single.url;
    document.getElementById('cat_image_preview_img').src    = single.thumbnail || single.url;
    document.getElementById('cat_image_preview').style.display = '';
    document.getElementById('cat_image_pick').style.display    = 'none';
});

document.getElementById('cat_image_remove').addEventListener('click', () => {
    document.getElementById('cat_image_url').value              = '';
    document.getElementById('cat_image_preview_img').src        = '';
    document.getElementById('cat_image_preview').style.display  = 'none';
    document.getElementById('cat_image_pick').style.display     = '';
});
</script>
@endpush
