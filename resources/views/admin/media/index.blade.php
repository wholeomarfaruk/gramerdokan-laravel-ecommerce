@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        {{-- ── Page header ───────────────────────────────────────────────── --}}
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Media Library</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Media Library</div></li>
            </ul>
        </div>

        {{-- ── Upload box ─────────────────────────────────────────────────── --}}
        <div class="wg-box mb-4">
            <div class="flex items-center justify-between mb-3">
                <div class="body-title">Upload New Files</div>
                <div class="text-tiny" style="color:#9ca3af;">
                    Supported: Images, Videos, Documents, Archives &nbsp;·&nbsp; Max 10 MB
                </div>
            </div>
            @livewire('admin.media.media-upload')
        </div>

        {{-- ── Library ─────────────────────────────────────────────────────── --}}
        @livewire('admin.media.media-library')

    </div>
</div>
@endsection

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    .media-card:hover {
        border-color: #2377FC !important;
        box-shadow: 0 4px 16px rgba(35,119,252,.15);
    }
</style>
@endpush
