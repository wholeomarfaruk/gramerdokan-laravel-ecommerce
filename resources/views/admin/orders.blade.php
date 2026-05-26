@extends('layouts.admin')

@section('content')
    <!-- content area start -->
    @livewire('admin.orders.order-list')
    <!-- content area end -->
@endsection
@push('scripts')
    <script>
        // Silently clean up drafts that already have a real order
        fetch("{{ route('admin.order.drafts.cleanup') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        }).catch(() => {});
    </script>
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
    <script>
        $("#bulk-select-button").click(function() {
            $(".select-item").toggle();

            $(".select-item").prop('checked', false);

        })

        document.getElementById('bulk-action-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const status = document.getElementById('bulk-action-status').value;
            console.log(status);
            if (status == '' || status == 'Select Status' || status == null) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No action status selected',
                    text: 'Please select a valid action status to perform.'
                });
                return;
            }
            var selected = document.querySelectorAll('input.select-item:checked');
            const ids = selected ? [...selected].map(el => el.value) : [];


            if (ids.length > 0) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete them!'
                }).then((result) => {
                    if (result.isConfirmed) {

                        fetch("{{ route('admin.orders.status.update.bulk') }}", {
                                method: 'put',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .content,
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    ids: ids,
                                    status: status,
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Order Status Updated Successfully',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    setTimeout(() => location.reload(), 1500);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.message || 'Something went wrong'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred while updating order statuses'
                                });
                            });
                    }
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'No selection',
                    text: 'Please select at least one enquiry to perform this action.'
                });
            }
        });

        document.getElementById('bulk-sticker-print').addEventListener('click', () => {
            console.log('clicked');
            const form = document.getElementById('sticker-print-form');
            const input = form.querySelector('input[name="ids"]');

            var selected = document.querySelectorAll('input.select-item:checked');
            const ids = selected ? [...selected].map(el => el.value) : [];

            if (ids.length > 0) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete them!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        input.value = ids;
                        form.submit();
                    }


                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'No selection',
                    text: 'Please select at least one enquiry to delete.'
                });
            }
        });
        document.getElementById('bulk-action-button').addEventListener('click', () => {
            console.log('clicked');

            var selected = document.querySelectorAll('input.select-item:checked');
            const ids = selected ? [...selected].map(el => el.value) : [];

            if (ids.length > 0) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete them!'
                }).then((result) => {
                    if (result.isConfirmed) {

                    }
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'No selection',
                    text: 'Please select at least one enquiry to delete.'
                });
            }
        });
    </script>
@endpush
