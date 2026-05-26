@extends('layouts.admin')

@section('content')
    <!-- content area start -->
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Locations</h3>
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
                        <mers class="text-tiny">Locations
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
                <form action="">
                    @if ($countries && $countries->count() > 0)
                        <fieldset>
                            <label for="">Select Country</label>
                            <select name="country_id" id="country_dropdown">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    @endif
                    @if ($states && $states->count() > 0 && !empty(request()->country_id) && request()->country_id > 0)
                        <fieldset>
                            <label for="">Select State</label>
                            <select name="state_id" id="state_dropdown">
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}" {{ request()->state_id == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    @endif
                    @if ($cities && $cities->count() > 0 && !empty(request()->state_id) && request()->state_id > 0)
                        <fieldset>
                            <label for="">Select City</label>
                            <select name="city_id" id="city_dropdown">
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}" {{ request()->city_id == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    @endif
                    @if ($police_stations && $police_stations->count() > 0 && !empty(request()->city_id) && request()->city_id > 0)
                        <fieldset>
                            <label for="">Select Police Station</label>
                            <select name="ps_id" id="police_station_dropdown">
                                @foreach ($police_stations as $ps)
                                    <option value="{{ $ps->id }}" {{ request()->ps_id == $ps->id ? 'selected' : '' }}>{{ $ps->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    @endif
                    @if ($zipcodes && $zipcodes->count() > 0 && !empty(request()->ps_id) && request()->ps_id > 0)
                        <fieldset>
                            <label for="">Select Zipcode</label>
                            <select name="zipcode_id" id="zipcode_dropdown">
                                @foreach ($zipcodes as $zipcode)
                                    <option value="{{ $zipcode->id }}" {{ request()->zipcode_id == $zipcode->id ? 'selected' : '' }}>{{ $zipcode->name }} - {{ $zipcode->code }}
                                    </option>
                                @endforeach
                            </select>
                        </fieldset>
                    @endif
                    @if ($area_keywords && $area_keywords->count() > 0 && !empty(request()->zipcode_id) && request()->zipcode_id > 0)
                     <div>
                        <label for="">Area Keywords</label>
                        <ol class="mt- " style="font-size: 14px; max-height: 200px; overflow-y: auto; padding-left: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
                            @foreach ($area_keywords as $ak)
                                <li class="mb-3">{{ $ak->name }}</li>
                            @endforeach
                        </ol>
                     </div>
                    @endif
                    <div class="button-submit">
                        <button class="" type="submit"><i class="icon-search"></i> Filter</button>
                    </div>
                </form>
            </div>
            {{-- <div class="wg-table table-all-user">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width:70px">ID</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Phone</th>
                                <th class="text-center">Devices</th>
                                <th class="text-center">Last Active</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                                <tr>
                                    <td class="text-center">{{ $customer->id }}</td>
                                    <td class="text-center">{{ $customer->first_name . ' ' . $customer->last_name }}</td>
                                    <td class="text-center">{{ $customer->phone }}</td>
                                    <td class="text-center">{{ $customer->devices->count() }}</td>
                                    <td class="text-center">
                                        {{ $customer->devices()->latest()->first()->last_activity ? $customer->devices()->latest()->first()->last_activity : 'Never' }}
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('admin.customers.details', $customer->id) }}">
                                            <div class="list-icon-function view-icon">
                                                <div class="item eye">
                                                    <i class="icon-eye"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div> --}}
            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{-- {{ $customers->links('pagination::bootstrap-5') }} --}}
            </div>
        </div>
    </div>
    </div>
    <!-- content area end -->
@endsection
