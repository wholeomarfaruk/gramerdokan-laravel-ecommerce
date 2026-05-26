@extends('layouts.admin')

@section('content')
    <!-- content area start -->
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Customers</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="index.html">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <mers class="text-tiny">Customers
            </div>
            </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." class="" name="search" tabindex="2"
                                value="" aria-required="true" required="">
                        </fieldset>
                        <div class="button-submit">
                            <button class="" type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="wg-box">
                <div class="flex items-center justify-start flex-wrap gap10">
                     <a class="tf-button style-1 text-capitalize" href="{{ route('admin.customers') }}">All
                            ({{$devices_count }})</a>

                       {{-- @foreach ($status_group as $sg)
                            <a class="tf-button style-1 text-capitalize {{ request()->order_status == $sg->status ? 'bg-primary text-white' : '' }}"
                                href="{{ route('admin.orders', ['order_status' => $sg->status]) }}">{{ $sg->status }}
                                ({{ $sg->count }})</a>
                        @endforeach --}}
                </div>
            </div>
            <div class="wg-box">
                <div class="accordion" id="accordionExample">
                  @foreach ($devices as $index => $device)


                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo{{$index}}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo{{$index}}" aria-expanded="false" aria-controls="collapseTwo{{$index}}">
                                {{ $device->device_model ." (". $device->device_type . ")" }} - last activity: {{ \Carbon\Carbon::parse($device->last_activity)->diffForHumans() }}
                            </button>
                        </h2>
                        <div id="collapseTwo{{$index}}" class="accordion-collapse collapse" aria-labelledby="headingTwo{{$index}}"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <style >
                                .table-bordered th {
                                  width: 150px;
                                }
                                </style>
                               <table class="table table-bordered">
                                <tr>
                                    <th>Entry ID</th>
                                    <td>{{ $device->id }}</td>
                                </tr>
                                <tr>
                                    <th>Device ID</th>
                                    <td>{{ $device->device_id }}</td>
                                </tr>
                                <tr>
                                    <th>Device Type</th>
                                    <td>{{ $device->device_type }}</td>
                                </tr>
                                <tr>
                                    <th>Device Model</th>
                                    <td>{{ $device->device_model }}</td>
                                </tr>
                                <tr>
                                    <th>Screen Size</th>
                                    <td>{{ $device->screen_size }}</td>
                                </tr>
                                <tr>
                                    <th>Customer ID</th>
                                    <td>{{ $device->customer_id ?? "N/A" }}</td>
                                </tr>
                                <tr>
                                    <th>User Agent</th>
                                    <td>{{ $device->user_agent }}</td>
                                </tr>
                                <tr>
                                    <th>Last Acitvity</th>
                                    <td>{{ $device->last_activity }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $device->created_at }}</td>
                                </tr>
                               </table>
                            </div>
                        </div>
                    </div>
  @endforeach
                </div>
            </div>
            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $devices->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    </div>
    <!-- content area end -->
@endsection
