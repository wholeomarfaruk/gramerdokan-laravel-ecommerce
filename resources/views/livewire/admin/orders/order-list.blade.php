<div>
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Orders</h3>
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
                        <div class="text-tiny">Orders</div>
                    </li>
                </ul>
            </div>

            <div class="wg-box">
                <div class="row ">
                    <div class="wg-filter col-md-6 mb-3">
                        <form class="form-search">
                            <fieldset class="name">
                                <input wire:model.live="search" type="text" placeholder="Search here..." class="" name="search"
                                    tabindex="2" value="" aria-required="true" required="">
                            </fieldset>
                            <div class="button-submit">
                                <button class="" type="submit"><i class="icon-search"></i></button>
                            </div>
                        </form>
                    </div>
                    <div class="wg-filter col-md-6 mb-3">
                        <form class="form-search" method="GET" action="{{ route('admin.orders.export') }}">
                            <fieldset class="name">
                                <select name="order_status" id="">
                                    <option value="">Select Status</option>

                                    @foreach ($status_group as $sg)
                                        <option value="{{ $sg->status }}">{{ $sg->status }} ({{ $sg->count }})
                                        </option>
                                    @endforeach

                                </select>
                            </fieldset>
                            <div class="button-submit">
                                <button class="" type="submit"><i class="icon-search"></i> Export</button>
                            </div>

                        </form>
                        <a class="tf-button style-1 w208" href="{{ route('admin.orders.add') }}"><i
                                class="icon-plus"></i>Add new</a>
                    </div>
                </div>
                <div class="wg-box">
                    <div class="flex items-center justify-start flex-wrap gap10">
                        <a class="tf-button style-1 text-capitalize" href="{{ route('admin.orders') }}">All
                            ({{ $orders_count }})</a>

                        @foreach ($status_group as $sg)
                            <a class="tf-button style-1 text-capitalize {{ request()->order_status == $sg->status ? 'bg-primary text-white' : '' }}"
                                href="{{ route('admin.orders', ['order_status' => $sg->status]) }}">{{ $sg->status }}
                                ({{ $sg->count }})
                            </a>
                        @endforeach
                    </div>

                </div>
                <div class="wg-box">
                    <form id="bulk-action-form" action="">

                        <div class="flex items-center flex-wrap justify-start gap20 mb-27">


                            <button type="button" class="btn btn-outline-secondary" id="bulk-select-button">Select</button>
                            <button type="button" class="btn btn-outline-secondary" id="all-select-button">All
                                Select</button>

                            <select class="form-control btn-outline-success" style="width: inherit;" name="status"
                                id="bulk-action-status" required>
                                <option selected>Select Status</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="processing">Processing</option>
                                <option value="in_transit">In Transit</option>
                                <option value="delivered">Delivered</option>
                                <option value="on_hold">On Hold</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="returned">Returned</option>
                                <option value="pending">Pending</option>
                                <option value="deleted">Delete</option>
                            </select>
                            <button id="bulk-action-button" type="submit" class="btn btn-outline-secondary">
                                Action
                            </button>

                        </div>
                    </form>
                    <form id="sticker-print-form" action="{{ route('admin.generate.sticker') }}" method="POST">
                        @csrf
                        <button id="bulk-sticker-print" type="button" class="btn btn-outline-secondary">
                            Print Stickers
                        </button>
                        <input type="text" name="ids" hidden>
                    </form>
                    <script>
                        var toggle = false;
                        document.getElementById('all-select-button').addEventListener('click', () => {

                            toggle = !toggle;
                            if (toggle) {
                                $('input.select-item').show();
                                document.querySelectorAll('input.select-item').forEach(el => el.checked = true);
                            } else {
                                $('input.select-item').hide();
                                document.querySelectorAll('input.select-item').forEach(el => el.checked = false);
                            }

                        });
                    </script>
                </div>
                <div class="wg-table table-all-user">
                    <div class="divider"></div>
                    <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                        {{ $orders->links() }}
                    </div>
                    <div class="table-responsive">
                        <style>
                            .select-item:checked {
                                background-color: #0d6efd !important;
                            }
                        </style>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:70px">OrderNo</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">Subtotal</th>
                                    <th class="text-center">Discount</th>
                                    <th class="text-center">Delivery charge</th>
                                    <th class="text-center">Total</th>

                                    <th class="text-center">Status</th>
                                    <th class="text-center">Order Date</th>
                                    <th class="text-center">Total Items</th>
                                    <th class="text-center">Delivered On</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td class="text-center" data-id="{{ $order->id }}"> <input type="checkbox"
                                                class="form-check-input select-item p-2" name="ids[] "
                                                value="{{ $order->id }}"
                                                style="display: none; z-index: 1; top:10px;left:10px; ">
                                            {{ $order->id }}</td>
                                        <td class="text-center">{{ $order->name }}
                                            @if ($order->isEventFired)
                                                <i class="icon-check-circle text-success"></i>
                                            @endif
                                            @if ($order->status == 'in_transit')
                                                <i class="icon-truck fs-3 "></i>
                                            @elseif($order->status == 'confirmed')
                                                <i class="icon-shopping-cart text-success fs-3"></i>
                                            @elseif($order->status == 'on_hold')
                                                <i class="icon-pause-circle text-warning fs-3"></i>
                                            @elseif($order->status == 'processing')
                                                <i class="icon-box text-warning fs-3"></i>
                                            @elseif($order->status == 'cancelled')
                                                <i class="icon-x-circle text-danger fs-3"></i>
                                            @elseif($order->status == 'delivered')
                                                <img style="height: 25px;" fill
                                                    src="{{ asset('admin-resource/icon/complete.png') }}"
                                                    alt="">
                                            @endif

                                        </td>
                                        <td class="text-center">{{ $order->phone }}</td>
                                        <td class="text-center">৳{{ $order->subtotal }}</td>
                                        <td class="text-center">৳{{ $order->discount }}</td>
                                        <td class="text-center">৳{{ $order->fee }}</td>

                                        <td class="text-center">৳{{ $order->total }}</td>


                                        <td class="text-center">{{ $order->status }}</td>
                                        <td class="text-center">{{ $order->created_at }}</td>
                                        <td class="text-center">{{ $order->Order_Item->count() }}</td>
                                        <td class="text-center">{{ $order->delivery_date }} </td>
                                        <td class="text-center">
                                            <div clas="d-flex justify-center gap-2 align-items-center flex-direction-row"
                                                style="display: flex; gap: 10px; justify-content: center; align-items: center; flex-direction: row;">
                                                <button class="btn btn-primary" wire:click="createPathaoOrder({{ $order->id }})" type="button" >
                                                     
                                                            <i class="icon-eye"></i> Pathao Entry
                                                     
                                                  
                                                </button>
                                                <a href="{{ route('admin.orders.details', $order->id) }}">
                                                    <div class="list-icon-function view-icon">
                                                        <div class="item eye">
                                                            <i class="icon-eye"></i>
                                                        </div>
                                                    </div>
                                                </a>
                                                <a href="{{ route('admin.orders.delete.soft', $order->id) }}">
                                                    <div class="list-icon-function">
                                                        <div class="item trash">
                                                            <i class="icon-trash"></i>
                                                        </div>
                                                    </div>
                                                </a>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div></div>
