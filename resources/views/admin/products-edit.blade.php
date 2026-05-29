@extends('layouts.admin')

@section('content')
    <!-- content area start -->
    <div class="main-content-inner">
        <!-- main-content-wrap -->
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Add Product</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <a href="{{ route('admin.products') }}">
                            <div class="text-tiny">Products</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Edit product</div>
                    </li>
                </ul>
            </div>
            <!-- form-add-product -->
            <form class="tf-section-2 form-add-product" method="POST"
                action="{{ route('admin.products.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $product->id }}">
                <div class="wg-box">
                    <fieldset class="name">
                        <div class="body-title mb-10">Product name <span class="tf-color-1">*</span>
                        </div>
                        <input class="mb-10 @error('name') is-invalid @enderror" type="text"
                            placeholder="Enter product name" name="name" tabindex="0" aria-required="true"
                            value="{{ $product->name }}" required autocomplete="name" autofocus>
                        <div class="text-tiny">Do not exceed 100 characters when entering the
                            product name.</div>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </fieldset>
                    <fieldset class="name">
                        <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                        <input class="mb-10 @error('slug') is-invalid @enderror" type="text" placeholder="Enter slug"
                            name="slug" tabindex="0" value="{{ $product->slug }}" aria-required="true"
                            required="required" onchange="stringtoSlug(this.value)" autofocus id="slug_input">
                        @error('slug')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </fieldset>


                    <div class="cols gap22">
                        <fieldset class="name">
                            <div class="body-title mb-10">Price <span class="tf-color-1">*</span></div>
                            <input class="mb-10 @error('price') is-invalid @enderror" type="text"
                                placeholder="Enter selling price" name="price" tabindex="0" value="{{ $product->price }}"
                                aria-required="true" required="required" autofocus>
                            @error('price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title mb-10">Discount Price</div>
                            <input class="mb-10 @error('discount_price') is-invalid @enderror" type="text"
                                placeholder="Enter discount price" name="discount_price" tabindex="0"
                                value="{{ $product->discount_price }}">
                            @error('discount_price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title mb-10">Purchase Price</div>
                            <input class="mb-10 @error('purchase_price') is-invalid @enderror" type="text"
                                placeholder="Enter purchase / cost price" name="purchase_price" tabindex="0"
                                value="{{ $product->purchase_price }}">
                            @error('purchase_price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </fieldset>
                    </div>
                    <div class="cols gap22">

                        <fieldset class="name">
                            <div class="body-title mb-10">Quantity <span class="tf-color-1">*</span>
                            </div>
                            <input class="mb-10 @error('quantity') is-invalid @enderror" type="text"
                                placeholder="Enter quantity" name="quantity" tabindex="0"
                                value="{{ $product->quantity }}" aria-required="true" required="required">
                            @error('quantity')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </fieldset>
                    </div>

                    <div class="cols gap22">
                        <fieldset class="name">
                            <div class="body-title mb-10">Stock</div>
                            <div class="select mb-10">
                                <select class="" name="stock_status">
                                    <option value="in_stock" {{ $product->stock_status == 'in_stock' ? 'selected' : '' }}>
                                        InStock</option>
                                    <option value="out_of_stock"
                                        {{ $product->stock_status == 'out_of_stock' ? 'selected' : '' }}>Out of Stock
                                    </option>
                                </select>
                            </div>
                            @error('stock_status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title mb-10">SKU <span class="tf-color-1">*</span>
                            </div>
                            <input class="mb-10 @error('sku') is-invalid @enderror" type="text"
                                placeholder="Enter SKU" name="sku" tabindex="0" value="{{ $product->sku }}"
                                aria-required="true" required="required">
                            @error('sku')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </fieldset>
                    </div>
                    <fieldset class="name">
                        <div class="body-title mb-10">Description <span class="tf-color-1">*</span></div>
                        <textarea id="editor" class="mb-10 @error('description') is-invalid @enderror" name="description" tabindex="0"
                            aria-required="true" required="required">{{ $product->description }}</textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </fieldset>
                    <fieldset class="name">
                        <div class="body-title mb-10">SEO Description <span class="tf-color-1">*</span></div>
                        <textarea class="mb-10 @error('short_description') is-invalid @enderror" name="short_description" tabindex="0"
                            aria-required="true" required="required">{{ $product->short_description }}</textarea>
                        @error('short_description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </fieldset>
                    <div class="cols gap22">

                        <fieldset class="name">
                            <div class="body-title mb-10">YT Video ID <span class="tf-color-1"></span>
                            </div>
                            <input class="mb-10 @error('yt_video_url') is-invalid @enderror" type="text"
                                placeholder="Enter YT Video ID" name="yt_video_url" tabindex="0"
                                value="{{ $product?->yt_video_url }}" aria-required="true" required="required">
                            @error('yt_video_url')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </fieldset>

                    </div>
                    {{-- <fieldset class="name">
                        <div class="body-title">Parent Category</div>
                        <div class="select flex-grow">
                            <select class=" @error('categories') is-invalid @enderror" name="categories" required>
                                <option value="">No categories</option>
                                @foreach ($categories as $category)
                                    <optgroup label="{{ $category->name }}">
                                        <option value="{{ $category->id }}"
                                            {{ old('categories', $product->categories->first()->id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}</option>
                                        @foreach ($category->children as $child)
                                            <option value="{{ $child->id }}"
                                                {{ old('categories', $product->categories->first()->id) == $child->id ? 'selected' : '' }}>
                                                {{ $child->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach

                            </select>

                        </div>
                        @error('categories')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </fieldset> --}}
                    <fieldset class="name">
                        <div class="body-title">Segment</div>
                        <div class="select flex-grow">
                            <select class=" @error('segment') is-invalid @enderror" name="segment">
                                <option value="">No segment</option>
                                @foreach ($segments as $segment)
                                    <option value="{{ $segment->id }}"
                                        {{ old('segment', $product?->segments?->first()?->id) == $segment->id ? 'selected' : '' }}>
                                        {{ $segment->name }}</option>
                                @endforeach

                            </select>

                        </div>
                        @error('parent_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </fieldset>
                      <div class="cols gap22">
                            <fieldset class="name">
                                <div class="body-title mb-10">Is Redirect</div>
                                <div class="select mb-10">
                                    <select class="" name="is_redirected">
                                        <option value="1"
                                            {{ old('is_redirected', $product?->is_redirected) == '1' ? 'selected' : '' }}>
                                            Enabled
                                        </option>
                                        <option value="0"
                                            {{ old('is_redirected', $product?->is_redirected) == '0' ? 'selected' : '' }}>
                                            Disabled</option>
                                    </select>
                                </div>
                                @error('is_redirected')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </fieldset>
                            <fieldset class="name">
                                <div class="body-title mb-10">Redirect Url
                                </div>
                                <input class="mb-10 @error('redirect_url') is-invalid @enderror" type="text"
                                    placeholder="Enter Redirect Url" name="redirect_url" tabindex="0"
                                    value="{{ old('redirect_url', $product?->redirect_url) }}" aria-required="true">
                                @error('redirect_url')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </fieldset>
                        </div>
                    <div class="cols gap10">
                        <button class="tf-button w-full" type="submit">Update product</button>
                    </div>





                </div>
                @php
                    // Backward compat: old records store just filename; new ones store full URL
                    $existingFeatured = $product->image
                        ? (str_starts_with($product->image, 'http') || str_starts_with($product->image, '/')
                            ? $product->image
                            : asset('storage/images/products/thumbnails/' . $product->image))
                        : '';
                @endphp
                <div class="wg-box">
                    {{-- Featured image --}}
                    <fieldset>
                        <div class="body-title mb-10">Featured image</div>
                        <input type="hidden" name="image" id="prod_image_url" value="{{ old('image', $existingFeatured) }}">
                        @error('image')
                            <span class="text-danger text-tiny d-block mb-2">{{ $message }}</span>
                        @enderror
                        <div id="prod_image_preview" style="{{ $existingFeatured ? '' : 'display:none;' }} margin-bottom:10px;">
                            <img id="prod_image_preview_img" src="{{ old('image', $existingFeatured) }}"
                                 style="height:80px;width:80px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;display:block;margin-bottom:6px;">
                            <button type="button" id="prod_image_remove" class="tf-button style-1" style="font-size:12px;padding:4px 12px;">
                                <i class="icon-x"></i> Remove
                            </button>
                        </div>
                        <button type="button" id="prod_image_pick" class="tf-button style-1"
                                style="{{ $existingFeatured ? 'display:none;' : '' }}"
                                onclick="Livewire.dispatch('open-media-picker', { multiple: false, callbackKey: 'prod_featured' })">
                            <i class="icon-image"></i> Choose from Media Library
                        </button>
                    </fieldset>

                    {{-- Gallery images — existing --}}
                    @php $galleryImages = $product->media()->where('category', 'product_images')->get(); @endphp
                    @if ($galleryImages->isNotEmpty())
                        <fieldset>
                            <div class="body-title mb-10">Current gallery images</div>
                            <div class="d-flex flex-wrap gap-2" id="existing-gallery">
                                @foreach ($galleryImages as $media)
                                    @php
                                        $thumbUrl = str_starts_with($media->path, 'storage/')
                                            ? asset($media->path)
                                            : $media->getThumbnailUrl();
                                    @endphp
                                    <div class="position-relative" id="media-{{ $media->id }}" style="width:90px;">
                                        <img src="{{ $thumbUrl }}" alt=""
                                            style="width:90px;height:90px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;">
                                        <button type="button"
                                            onclick="deleteGalleryImage({{ $media->id }})"
                                            class="btn btn-danger btn-sm position-absolute"
                                            style="top:2px;right:2px;padding:1px 5px;font-size:11px;line-height:1.4;"
                                            title="Remove">✕</button>
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>
                    @endif

                    {{-- Gallery images — add new from media library --}}
                    <fieldset>
                        <div class="body-title mb-10">Add gallery images</div>
                        <div id="gallery_preview" class="d-flex flex-wrap gap-2 mb-2"></div>
                        <button type="button" class="tf-button style-1"
                                onclick="Livewire.dispatch('open-media-picker', { multiple: true, callbackKey: 'prod_gallery' })">
                            <i class="icon-images"></i> Add from Media Library
                        </button>
                    </fieldset>
                    <div class="cols gap22">
                        <fieldset class="name">
                            <div class="body-title mb-10">Featured</div>
                            <div class="select mb-10">
                                <select class="" name="featured">
                                    <option value="0" {{ $product->featured == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ $product->featured == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                            @error('featured')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </fieldset>
                    </div>
                    <div class="cols gap22">
                        <fieldset class="name">
                            <div class="body-title mb-10">Is Active</div>
                            <div class="checkbox mb-10">
                                <input type="checkbox" name="status" id=""
                                    {{ $product->status == 1 ? 'checked' : '' }}>
                            </div>
                            @error('featured')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </fieldset>
                    </div>
                    <div>
                        <h2 class="" style="font-size: 16px;">Product Sizes</h2>
                        <div id="sizes-container">
                            @if ($product->sizes()->count() > 0)
                                @foreach ($product->sizes as $sizeId => $size)
                                    <div class="size-row mb-5 d-flex justify-content-between align-items-center"
                                        data-id="{{ $size->id }}">
                                        <input type="text" name="sizes[{{ $sizeId }}][size]"
                                            value="{{ $size->name }}" placeholder="Enter size">
                                        <input type="text" name="sizes[{{ $sizeId }}][qty]"
                                            value="{{ $size->quantity }}" placeholder="Enter Quantity">

                                        <button type="button" onclick="deleteSize({{ $size->id }})"> <i
                                                class="icon-trash-2 text-danger"></i></button>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <button type="button" onclick="addSize()">+ Add Size</button>

                    </div>




                </div>
            </form>
            <!-- /form-add-product -->
        </div>
        <!-- /main-content-wrap -->
    </div>
    <!-- content area end -->
    @livewire('admin.media.media-picker')
@endsection
@push('scripts')
    <script>
        // ── Featured image ────────────────────────────────────────────────────
        window.addEventListener('media-picker-confirmed', e => {
            const payload = e.detail[0] ?? e.detail;

            if (payload.callbackKey === 'prod_featured') {
                const single = payload.single;
                if (!single) return;
                document.getElementById('prod_image_url').value        = single.url;
                document.getElementById('prod_image_preview_img').src  = single.thumbnail || single.url;
                document.getElementById('prod_image_preview').style.display = '';
                document.getElementById('prod_image_pick').style.display    = 'none';
            }

            if (payload.callbackKey === 'prod_gallery') {
                const grid = document.getElementById('gallery_preview');
                const form = grid.closest('form');
                payload.media.forEach(item => {
                    if (form.querySelector(`input[name="gallery_media_ids[]"][value="${item.id}"]`)) return;
                    const wrap = document.createElement('div');
                    wrap.style.cssText = 'position:relative;width:72px;';
                    wrap.innerHTML = `
                        <img src="${item.thumbnail || item.url}"
                             style="width:72px;height:72px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;">
                        <button type="button"
                                onclick="this.closest('div').remove(); this.closest('form').querySelector('input[value=\\'${item.id}\\']')?.remove();"
                                style="position:absolute;top:2px;right:2px;background:#ef4444;border:none;border-radius:50%;color:#fff;width:18px;height:18px;font-size:11px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
                        <input type="hidden" name="gallery_media_ids[]" value="${item.id}">
                    `;
                    grid.appendChild(wrap);
                });
            }
        });

        document.getElementById('prod_image_remove').addEventListener('click', () => {
            document.getElementById('prod_image_url').value              = '';
            document.getElementById('prod_image_preview_img').src        = '';
            document.getElementById('prod_image_preview').style.display  = 'none';
            document.getElementById('prod_image_pick').style.display     = '';
        });

        // ── Delete existing gallery image ────────────────────────────────────
        function deleteGalleryImage(mediaId) {
            if (!confirm('Remove this image?')) return;

            fetch(`/admin/products/media/${mediaId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const el = document.getElementById(`media-${mediaId}`);
                    if (el) el.remove();
                    const grid = document.getElementById('existing-gallery');
                    if (grid && grid.children.length === 0) {
                        grid.closest('fieldset').remove();
                    }
                }
            })
            .catch(() => alert('Failed to delete image. Please try again.'));
        }

        function stringtoSlug(str) {
            str = str.replace(/^\s+|\s+$/g, '')
                     .toLowerCase()
                     .replace(/[^a-z0-9 -]/g, '')
                     .replace(/\s+/g, '-')
                     .replace(/-+/g, '-');
            const el = document.getElementById('slug_input');
            if (el) el.value = str;
        }
    </script>
    <script src="https://cdn.tiny.cloud/1/hkkbs6irhd8pjbxo4xgcyy5o1lvtjcx4p843koiprxzql6dh/tinymce/8/tinymce.min.js"
        referrerpolicy="origin" crossorigin="anonymous"></script>

    <script>
        tinymce.init({
            selector: '#editor',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',


        });
    </script>
    <script>
        let sizeId = {{ $product->sizes()->count() }};

        function addSize(value = "") {
            sizeId++;
            const container = document.getElementById("sizes-container");

            const div = document.createElement("div");

            div.classList.add('size-row', 'mb-5', 'd-flex', 'justify-content-between', 'align-items-center');
            div.setAttribute("data-id", sizeId);

            div.innerHTML = `
        <input type="text" name="sizes[${sizeId}][size]" value="${value}" placeholder="Enter size">
        <input type="text" name="sizes[${sizeId}][qty]" value="${value}" placeholder="Enter Quantity">

        <button type="button" onclick="deleteSize(${sizeId})">   <i class="icon-trash-2 text-danger"></i></button>
      `;
            container.appendChild(div);
        }

        function deleteSize(id) {
            const row = document.querySelector(`.size-row[data-id='${id}']`);
            if (row) row.remove();
        }

        function editSize(id) {
            const row = document.querySelector(`.size-row[data-id='${id}'] input`);
            if (row) {
                const newValue = prompt("Edit size:", row.value);
                if (newValue !== null) row.value = newValue;
            }
        }
    </script>
@endpush
