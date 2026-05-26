@extends('layouts.admin')

@section('content')
    <!-- content area start -->
    <style>
        .table-transaction>tbody>tr:nth-of-type(odd) {
            --bs-table-accent-bg: #fff !important;
        }
    </style>
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Customer Details</h3>
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
                        <div class="text-tiny">Customer List</div>
                    </li>
                </ul>
            </div>
            <div class="wg-box mt-5 mb-27">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">

                    </div>
                    <a class="tf-button style-1 w208" data-bs-toggle="modal"
                                    data-bs-target="#orderDetails" href="javascript:void(0);">Update Profile</a>
                    <a class="tf-button style-1 w208" href="{{ route('admin.orders') }}">Back</a>
                </div>
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <h5>Customer Details</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-transaction">
                        <tbody>

                            <tr>
                                <th>Customer ID</th>
                                <td>{{ $customer->id }}</td>
                                <th>Customer Name</th>
                                <td>{{ $customer->first_name . ' ' . $customer->last_name }}</td>
                                <th>Mobile No</th>
                                <td>{{ $customer->phone }}</td>

                            </tr>
                            <tr>
                                <th>total Order</th>
                                <td>{{ $customer->Orders->count() }}</td>
                                <th>Total Delivered Order</th>
                                <td>{{ $customer->Orders->where('status', 'delivered')->count() }}</td>
                                <th>Status</th>
                                <td>{{ $customer->status }}</td>
                            </tr>
                            <tr>
                                <th>Order Date</th>
                                <td>{{ $customer->created_at }}</td>
                                <th>Last Order Date</th>
                                <td>{{ $customer->Orders->last()->created_at }}</td>
                                <th>Total Delivered orders</th>
                                <td>{{ $customer->Orders->where('status', 'cancelled')->count() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="wg-box">
                <h5>Ordere List</h5>
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
                                    <td class="text-center">{{ $order->name }}</td>
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

                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            </div>

            <div class="wg-box mt-5">
                <div class="tf-section-1 mb-30">
                    <div class="flex gap20 flex-wrap-mobile">
                        <div class="w-half">
                            <h5>Profile Details
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#orderDetails">
                                    Edit
                                </button>
                            </h5>
                            <div class="my-account__address-item col-md-6">
                                <div class="my-account__address-item__detail">
                                    <p>Name : {{ $customer->first_name . ' ' . $customer->last_name }}</p>
                                    <p>Mobile : {{ $customer->phone }}</p>
                                    <p>Mobile : {{ $customer->email }}</p>
                                    <p>Mobile : {{ $customer->gender }}</p>



                                    <p>country : {{ $customer->country }}</p>
                                    <p>State : {{ $customer->state }}</p>
                                    <p>city : {{ $customer->city }}</p>
                                    <p>Zip Code : {{ $customer->zip_code }}</p>
                                    <p>Street : {{ $customer->street }}</p>
                                    <p>Full Address : {{ $customer->address }}</p>
                                    <br>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>


            {{-- <div class="wg-box mt-5">
                <h5>Update Order Status</h5>
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">

                                <select name="status" id="status" class="form-control">
                                    <option value="pending" @if ($order->status == 'pending') selected @endif>Pending
                                    </option>
                                    <option value="confirmed" @if ($order->status == 'confirmed') selected @endif>Confirmed
                                    </option>
                                    <option value="delivered" @if ($order->status == 'delivered') selected @endif>Delivered
                                    </option>
                                    <option value="on_hold" @if ($order->status == 'on_hold') selected @endif>on_hold
                                    </option>
                                    <option value="in_transit" @if ($order->status == 'in_transit') selected @endif>in_transit
                                    </option>
                                    <option value="processing" @if ($order->status == 'processing') selected @endif>Processing
                                    </option>
                                    <option value="cancelled" @if ($order->status == 'cancelled') selected @endif>Cancelled
                                    </option>
                                    <option value="returned" @if ($order->status == 'returned') selected @endif>Returned
                                    </option>
                                </select>

                            </div>

                        </div>
                        <div class="col-md-3">
                            <div class="d-flex">
                                <div class="form-group mr-2">
                                    <input type="text" name="last_name">
                                </div>
                                <div class="form-group ml-2">
                                    <input type="text" name="last_name">
                                </div>
                            </div>


                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="gender" id="">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-9">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>

                </form>

            </div> --}}
            <div class="wg-box mt-5">
                <h5>Extra Data</h5>
                <div class="my-account__address-item col-md-6">
                    <div class="my-account__address-item__detail">
                        <p>IP Address: {{ $order->ip_address }}</p>
                        <p>User Agent: {{ $order->user_agent }}</p>

                        <pre> {{ json_encode($order->json_data, JSON_PRETTY_PRINT) }}</pre>
                        <br>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="orderDetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST"
                        id="orderDetailForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">First Name</label>
                                    <input type="text" name="first_name" class="form-control" id="name"
                                        value="{{ $customer->first_name }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" id="name"
                                        value="{{ $customer->last_name }}">
                                </div>
                            </div>
                            <hr class="mt-3 mb-2" style="border-color:transparent; background-color: transparent;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">customer Phone</label>
                                    <input type="text" name="phone" class="form-control" id="phone"
                                        value="{{ $customer->phone }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">customer Email</label>
                                    <input type="text" name="email" class="form-control" id="phone"
                                        value="{{ $customer->email }}">
                                </div>
                            </div>
                            <hr class="mt-3 mb-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select name="gender" class="form-control" id="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ $customer->gender == 'male' ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="female" {{ $customer->gender == 'female' ? 'selected' : '' }}>
                                            Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city"> Country</label>
                                    <select name="country" id="status" class="form-control">
                                        <option value="">Select Country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->short_code }}"
                                                @if ($country->short_code == $customer->country) selected @endif>{{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                            <hr class="mt-3 mb-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="states">states</label>
                                    <select name="state" class="form-control" id="states">
                                        <option value="">Select State</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->name }}"
                                                @if ($state->name == $customer->state) selected @endif>{{ $state->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city"> City</label>
                                    <select name="city" id="status" class="form-control">

                                        <option value="">Select city</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->name }}"
                                                @if ($city->name == $customer->city) selected @endif>{{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                            <hr class="mt-3 mb-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="zip_code">Zip Code</label>
                                    <input type="text" name="zip_code" class="form-control" id="zip_code"
                                        value="{{ $customer->zip_code }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="streat">Street</label>
                                    <input type="text" name="street" class="form-control" id="street"
                                        value="{{ $customer->street }}">
                                </div>
                            </div>
                            <hr class="mt-3 mb-2" style="border-color:transparent; background-color: transparent;">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">Full Address</label>
                                    <textarea name="address" class="form-control" id="address" cols="30" rows="10">{{ $customer->address }}</textarea>
                                </div>
                            </div>

                        </div>

                    </form>
                </div>

                <!-- Footer -->
                <div class="modal-footer flex-column align-items-stretch">

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary"
                            onclick="document.getElementById('orderDetailForm').submit();">
                            Save Changes
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- content area end -->
@endsection
