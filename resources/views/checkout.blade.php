@extends('layouts.app')

@section('content')
   @livewire('website.checkout')
@endsection

@push('scripts')
    <script>
        (function () {
            var autosaveTimer = null;

            function checkoutAutosave() {
                var name     = document.querySelector('[name="name"]')?.value || '';
                var phone    = document.querySelector('[name="phone"]')?.value || '';
                var address  = document.querySelector('[name="address"]')?.value || '';
                var note     = document.querySelector('[name="note"]')?.value || '';
                var area     = document.querySelector('[name="delivery_area_id"]')?.value || '';
                var payment  = document.querySelector('[name="payment_method"]:checked')?.value || 'cod';
                var token    = document.querySelector('meta[name="csrf-token"]')?.content || '';

                if (!phone) return;

                fetch('{{ route("cart.order.autosave.checkout") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        name: name,
                        phone: phone,
                        address: address,
                        note: note,
                        delivery_area_id: area,
                        payment_method: payment
                    })
                }).catch(function () {});
            }

            function debounce() {
                clearTimeout(autosaveTimer);
                autosaveTimer = setTimeout(checkoutAutosave, 1500);
            }

            // Text / textarea inputs — fire after user pauses typing
            document.addEventListener('input', function (e) {
                var n = e.target.getAttribute('name');
                if (n === 'name' || n === 'phone' || n === 'address' || n === 'note') {
                    debounce();
                }
            });

            // Select & radio — fire immediately on change
            document.addEventListener('change', function (e) {
                var n = e.target.getAttribute('name');
                if (n === 'delivery_area_id' || n === 'payment_method') {
                    debounce();
                }
            });
        })();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const paymentInputs = document.querySelectorAll('input[name="payment_method"]');
            const bkashCard = document.getElementById('bkash-instructions-card');

            const toggleBkashInstructions = () => {
                const selectedValue = document.querySelector('input[name="payment_method"]:checked')?.value;
                bkashCard?.classList.toggle('d-none', selectedValue !== 'bkash');
            };

            paymentInputs.forEach((input) => {
                input.addEventListener('change', toggleBkashInstructions);
            });

            toggleBkashInstructions();
        });
    </script>
@endpush
