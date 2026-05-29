@php
    $depth      = $depth ?? 0;
    $hasChildren = $category->childrenRecursive->isNotEmpty();
    $collapseId  = 'cat-' . $category->id;
    $productCount = $category->products_count ?? 0;
    $isActive    = $category->is_active ?? 1;
    $depthClass  = 'cat-depth-' . min($depth, 3);

    $imgSrc = $category->getImageUrl();
@endphp

<div class="cat-node {{ $depthClass }}">

    {{-- Row --}}
    <div class="cat-row">

        {{-- Toggle caret --}}
        @if ($hasChildren)
            <button
                class="cat-toggle"
                data-bs-toggle="collapse"
                data-bs-target="#{{ $collapseId }}"
                aria-expanded="true"
                type="button">
                <i class="icon-chevron-right cat-caret"></i>
            </button>
        @else
            <span class="cat-no-toggle"></span>
        @endif

        {{-- Thumbnail --}}
        @if ($imgSrc)
            <img src="{{ $imgSrc }}" class="cat-thumb" alt="{{ $category->name }}">
        @else
            <div class="cat-thumb-placeholder">
                <i class="{{ $hasChildren ? 'icon-layers' : 'icon-grid' }}"></i>
            </div>
        @endif

        {{-- Name + slug --}}
        <div class="cat-info">
            <span class="cat-name">{{ $category->name }}</span>
            <span class="cat-slug">/{{ $category->slug }}</span>
        </div>

        {{-- Badges --}}
        <div class="cat-badges d-none d-md-flex">
            @if ($hasChildren)
                <span class="badge bg-light text-secondary border" style="font-size:11px;">
                    {{ $category->childrenRecursive->count() }} sub
                </span>
            @endif
            <span class="badge bg-light text-secondary border" style="font-size:11px;"
                title="{{ $productCount }} products">
                {{ $productCount }} <i class="icon-shopping-cart" style="font-size:10px;"></i>
            </span>
            @if ($isActive)
                <span class="badge bg-success" style="font-size:10px;">Active</span>
            @else
                <span class="badge bg-secondary" style="font-size:10px;">Inactive</span>
            @endif
        </div>

        {{-- Actions --}}
        <div class="cat-actions">
            <a href="{{ route('category.show', $category->slug) }}"
               target="_blank"
               class="btn btn-outline-primary"
               title="View on site">
                <i class="icon-eye"></i>
            </a>
            <a href="{{ route('admin.categories.manage.products', $category->id) }}"
               class="btn btn-outline-secondary"
               title="Manage products">
                <i class="icon-shopping-cart"></i>
            </a>
            <a href="{{ route('admin.categories.edit', $category->id) }}"
               class="btn btn-outline-warning"
               title="Edit">
                <i class="icon-edit-3"></i>
            </a>
            <form action="{{ route('admin.categories.delete', $category->id) }}"
                  method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger cat-delete" title="Delete">
                    <i class="icon-trash-2"></i>
                </button>
            </form>
        </div>

    </div>{{-- end .cat-row --}}

    {{-- Children (recursive) --}}
    @if ($hasChildren)
        <div class="collapse show" id="{{ $collapseId }}">
            <div class="cat-children">
                @foreach ($category->childrenRecursive as $child)
                    @include('admin.category.tree', [
                        'category' => $child,
                        'depth'    => $depth + 1,
                    ])
                @endforeach
            </div>
        </div>
    @endif

</div>{{-- end .cat-node --}}
