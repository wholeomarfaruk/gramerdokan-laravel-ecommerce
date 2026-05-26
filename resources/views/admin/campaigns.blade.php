@extends('layouts.admin')

@section('content')
    <!-- content area start -->
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>All Products</h3>
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
                        <div class="text-tiny">All Products</div>
                    </li>
                </ul>
            </div>

            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <form class="form-search">
                            <fieldset class="name">
                                <input type="text" placeholder="Search here..." class="" name="search" value="{{ request()?->search }}"
                                    tabindex="2" value="" aria-required="true" required="">
                            </fieldset>
                            <div class="button-submit">
                                <button class="" type="submit"><i class="icon-search"></i></button>
                            </div>
                        </form>
                    </div>
                    <a class="tf-button style-1 w208" href="{{ route('admin.campaigns.add') }}"><i class="icon-plus"></i>Add
                        new</a>
                </div>
                <div class="table-responsive">
                    @if (Session::has('status'))
                        <div class="alert alert-success" role="alert">
                            {{ Session::get('status') }}
                        </div>
                    @endif
                    <table class="table table-striped table-bordered">
                        <thead>

                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Published</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($campaigns as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td class="pname">
                                        <div class="image">
                                            {{-- <img src="{{ asset('storage/images/products/thumbnails/' . $product->image) }}"
                                                alt="{{ $product->name }}" class="image"> --}}
                                        </div>
                                        <div class="name">
                                            <a target="_blank"
                                                href="{{ route('campaign.details',['slug' => $product->slug]) }}"
                                                class="body-title-2">{{ $product->name }}</a>
                                            <div class="text-tiny mt-3">{{ $product->slug }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $product->status == 1 ? 'Published' : 'Unpublished' }}</td>
                                    <td>
                                        <div class="list-icon-function">

                                            <a href="{{ route('admin.campaigns.edit', ['id' => $product->id]) }}">
                                                <div class="item edit">
                                                    <i class="icon-edit-3"></i>
                                                </div>
                                            </a>
                                            <form action="{{ route('admin.campaigns.delete', ['id' => $product->id]) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class="item text-danger delete">
                                                    <i class="icon-trash-2"></i>
                                                </div>
                                            </form>
                                            <!-- Default dropstart button -->
                                            <div class="btn-group dropstart">
                                                <button type="button" class="btn btn-secondary dropdown-toggle"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    More
                                                </button>
                                                <ul class="dropdown-menu">
                                                    {{-- <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.products.copy', ['id' => $product->id]) }}">Copy
                                                            Product</a>
                                                    </li> --}}
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.campaigns.landingpage.edit', ['id' => $product->id]) }}">Landing Page Edit</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">

                    {{ $campaigns->links('pagination::bootstrap-5') }}

                </div>
            </div>
        </div>
    </div>
    <!-- content area end -->
@endsection
@push('scripts')
    <script>
        $('.delete').click(function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            var name = $(this).closest('tr').find('.pname').text();
            if (confirm("Are you sure? You want to delete " + name)) {
                form.submit();
            }
        })
    </script>
@endpush
