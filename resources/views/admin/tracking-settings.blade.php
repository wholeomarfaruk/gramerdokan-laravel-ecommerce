@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Tracking Settings</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Tracking Settings</div></li>
            </ul>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif

        <form class="form-style-1" action="{{ route('admin.tracking.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            {{-- ── Meta Default ──────────────────────────────────────────────── --}}
            <div class="wg-box mb-4">
                <h5 class="mb-1">Meta / Facebook — Default</h5>
                <p class="text-tiny text-muted mb-3">Used by CAPI classes as the fallback pixel.</p>

                <fieldset class="name">
                    <div class="body-title">Pixel ID</div>
                    <input class="flex-grow" type="text" name="META_PIXEL_ID"
                        value="{{ $settings['META_PIXEL_ID'] }}" placeholder="e.g. 4103454409917132">
                </fieldset>

                <fieldset class="name">
                    <div class="body-title">Access Token</div>
                    <input class="flex-grow" type="password" name="META_ACCESS_TOKEN"
                        value="{{ $settings['META_ACCESS_TOKEN'] }}" placeholder="EAAxxxxxxxx...">
                </fieldset>

                <fieldset class="name">
                    <div class="body-title">Test Event Code</div>
                    <input class="flex-grow" type="text" name="META_TEST_EVENT_CODE"
                        value="{{ $settings['META_TEST_EVENT_CODE'] }}" placeholder="e.g. TEST12345">
                </fieldset>
            </div>

            {{-- ── GTM ───────────────────────────────────────────────────────── --}}
            <div class="wg-box mb-4">
                <h5 class="mb-1">Google Tag Manager</h5>
                <p class="text-tiny text-muted mb-3">Injected into all website and app layouts.</p>

                <fieldset class="name">
                    <div class="body-title">GTM Container ID</div>
                    <input class="flex-grow" type="text" name="GOOGLE_TAG_MANAGER_ID"
                        value="{{ $settings['GOOGLE_TAG_MANAGER_ID'] }}" placeholder="e.g. GTM-5XWHPZQH">
                </fieldset>
            </div>

            {{-- ── TikTok ────────────────────────────────────────────────────── --}}
            <div class="wg-box mb-4">
                <h5 class="mb-1">TikTok Pixel</h5>
                <p class="text-tiny text-muted mb-3">Browser-side pixel injected into layouts.</p>

                <fieldset class="name">
                    <div class="body-title">TikTok Pixel ID</div>
                    <input class="flex-grow" type="text" name="TIKTOK_PIXEL_ID"
                        value="{{ $settings['TIKTOK_PIXEL_ID'] }}" placeholder="e.g. D6IQ9H3C77U5KIUERMS0">
                </fieldset>
            </div>

            {{-- ── Queue ─────────────────────────────────────────────────────── --}}
            <div class="wg-box mb-4">
                <h5 class="mb-1">Queue</h5>

                <fieldset class="name">
                    <div class="body-title">Meta CAPI Queue Name</div>
                    <div class="flex-grow">
                        <code style="font-size:1rem; padding: 10px 14px; background:#f4f4f4; border:1px solid #ddd; border-radius:6px; display:block;">
                            {{ $settings['META_CAPI_QUEUE'] }}
                        </code>
                        <div class="text-tiny mt-2" style="color:#888;">
                            Read-only. Change <code>META_CAPI_QUEUE</code> in <code>.env</code> to update.
                        </div>
                    </div>
                </fieldset>

                <fieldset class="name mt-3">
                    <div class="body-title">Queue Worker Command</div>
                    <div class="flex-grow">
                        <code style="font-size:0.9rem; padding: 10px 14px; background:#1e1e1e; color:#d4d4d4; border-radius:6px; display:block; user-select:all;">
                            php artisan queue:work --queue={{ $settings['META_CAPI_QUEUE'] }} --sleep=3 --tries=3
                        </code>
                        <div class="text-tiny mt-2" style="color:#888;">
                            Run this command on the server to process CAPI events.
                        </div>
                    </div>
                </fieldset>

                <fieldset class="name mt-3">
                    <div class="body-title">Cron / Supervisor Config Path</div>
                    <div class="flex-grow">
                        <code style="font-size:0.9rem; padding: 10px 14px; background:#f4f4f4; border:1px solid #ddd; border-radius:6px; display:block;">
                            /etc/supervisor/conf.d/laravel-worker.conf
                        </code>
                        <div class="text-tiny mt-2" style="color:#888;">
                            Laravel scheduler cron entry:
                        </div>
                        <code style="font-size:0.9rem; padding: 10px 14px; background:#1e1e1e; color:#d4d4d4; border-radius:6px; display:block; margin-top:6px; user-select:all;">
                            * * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1
                        </code>
                    </div>
                </fieldset>
            </div>

            <div class="wg-box">
                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Save Settings</button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection
