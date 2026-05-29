@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Segments</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Segments</div></li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow"></div>
                <a class="tf-button style-1 w208" href="{{ route('admin.segments.add') }}">
                    <i class="icon-plus"></i>Add new
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success mt-3">{{ session('success') }}</div>
            @endif

            <div class="wg-table mt-3">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Products</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($segments as $segment)
                            <tr>
                                <td>{{ $segment->id }}</td>
                                <td>{{ $segment->name }}</td>
                                <td><span class="text-tiny">{{ $segment->slug }}</span></td>
                                <td>{{ $segment->products_count }}</td>
                                <td>
                                    @if($segment->is_active)
                                        <span class="badge bg-success rounded-pill">Active</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="list-icon-function">
                                        <a href="{{ route('admin.segments.products', $segment->id) }}" title="Manage Products">
                                            <div class="item"><i class="icon-layers"></i></div>
                                        </a>
                                        <a href="{{ route('admin.segments.edit', $segment->id) }}" title="Edit">
                                            <div class="item edit"><i class="icon-edit-3"></i></div>
                                        </a>
                                        <button type="button"
                                            class="btn-delete border-0 bg-transparent text-danger"
                                            data-id="{{ $segment->id }}"
                                            data-name="{{ $segment->name }}"
                                            title="Delete">
                                            <div class="item"><i class="icon-trash-2"></i></div>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No segments found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.btn-delete').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id   = this.dataset.id;
            var name = this.dataset.name;

            Swal.fire({
                title: 'Delete "' + name + '"?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then(function(result) {
                if (!result.isConfirmed) return;

                fetch('/admin/segments/' + id + '/delete', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        Swal.fire('Deleted!', data.message, 'success')
                            .then(function() { location.reload(); });
                    } else {
                        Swal.fire({
                            title: 'Cannot Delete',
                            text: data.message,
                            icon: 'error',
                            confirmButtonColor: '#3085d6',
                        });
                    }
                })
                .catch(function() {
                    Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                });
            });
        });
    });
</script>
@endpush
