@extends('layouts.admin')

@section('content')
    <!-- content area start -->
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Admin Terminal</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="#">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <a href="#">
                            <div class="text-tiny">Admin Terminal</div>
                        </a>
                    </li>
       
                </ul>
            </div>
            <!-- new-category -->
            <div class="wg-box">
                @livewire('admin.admin-terminal')
            </div>
        </div>
    </div>
    <!-- content area end -->
@endsection


