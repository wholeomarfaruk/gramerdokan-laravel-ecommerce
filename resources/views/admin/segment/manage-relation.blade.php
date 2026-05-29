@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Manage Products — {{ $segment->name }}</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('admin.segments') }}"><div class="text-tiny">Segments</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Manage Products</div></li>
            </ul>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif

        {{-- Assign products --}}
        <div class="wg-box mb-4">
            <h5 class="mb-3">Assign Products</h5>
            <form action="{{ route('admin.segments.products.assign', $segment->id) }}" method="POST">
                @csrf
                <fieldset class="name">
                    <div class="body-title">Select Products</div>
                    <div class="select flex-grow">
                        <select id="products" class="selectpicker @error('products') is-invalid @enderror"
                            name="products[]" multiple data-live-search="true" title="Choose products...">
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->id }} — {{ $product->name }} — {{ $product->discount_price ?? $product->price }} Tk
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('products')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </fieldset>
                <div class="bot mt-3">
                    <div></div>
                    <button class="tf-button w208" type="submit">Assign</button>
                </div>
            </form>
        </div>

        {{-- Assigned products list --}}
        <div class="wg-box">
            <h5 class="mb-3">Assigned Products ({{ $segmentProducts->count() }})</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>SKU</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($segmentProducts as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>
                                <div class="flex items-center gap10">
                                    <div class="name">
                                        <a target="_blank" href="{{ route('product.show', [$segment->slug, $product->slug]) }}"
                                            class="body-title-2">{{ $product->name }}</a>
                                        <div class="text-tiny mt-1">{{ $product->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->discount_price ?? $product->price }} Tk</td>
                            <td>{{ $product->sku }}</td>
                            <td>
                                @if($product->status)
                                    <span class="badge bg-success rounded-pill">Active</span>
                                @else
                                    <span class="badge bg-danger rounded-pill">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.segments.products.unassign', $segment->id) }}"
                                    method="POST" class="d-inline unassign-form">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="products" value="{{ $product->id }}">
                                    <button type="button" class="btn-unassign border-0 bg-transparent text-danger" title="Remove">
                                        <div class="item"><i class="icon-trash-2"></i></div>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No products assigned yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#products').selectpicker();

        document.querySelectorAll('.btn-unassign').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var form = this.closest('form');
                Swal.fire({
                    title: 'Remove this product?',
                    text: 'It will be unassigned from this segment.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, remove it!'
                }).then(function(result) {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    });
</script>
@endpush
