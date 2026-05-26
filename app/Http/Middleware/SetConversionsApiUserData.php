<?php

namespace App\Http\Middleware;

use App\Helper\MetaHelper;
use App\Meta\MetaBaseData;
use App\Models\Customer;
use App\Models\Device;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Esign\ConversionsApi\Facades\ConversionsApi;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Support\Facades\Log;

class SetConversionsApiUserData
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cookieData = json_decode($request->cookie('master_dl'), true) ?? [];
        $user = null;
        $custom_fbc = null;
        if ($request->cookie('_sfdid') && $request->cookie('_sfud') == null) {
            $user = Device::where('device_id', $request->cookie('_sfdid'))->first()->customer;

            if ($user) {
                $user = $user->only([
                    'id',
                    'email',
                    'first_name',
                    'last_name',
                    'phone',
                    'email',
                    'gender',
                    'address',
                    'country',
                    'state',
                    'city',
                    'zip_code',
                    'street',
                ]);
            }

        }
        if($request->get('fbclid')){
            $request->merge(['custom_fbc' => MetaHelper::format_new_fbc($request->get('fbclid'))]);
        }

        $masterData = array_merge($cookieData, [
            'fbp' => $request->cookie('_fbp') ?? $request->get('fbp') ?? ($cookieData['fbp'] ?? null),
            'fbc' => $request->cookie('custom_fbc') ?? ($custom_fbc ? $custom_fbc : (isset($cookieData['fbc']) ? $cookieData['fbc'] : ($request->get('_fbc') ?? null))),
            'ttclid' => $request->get('ttclid') ?? ($cookieData['ttclid'] ?? null),
            'ttp' => $request->cookie('_ttp') ?? ($cookieData['ttp'] ?? null),
            'referrer_url' => $request->header('Referer') ?? ($cookieData['referrer_url'] ?? null),
            'client_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_id' => $request->cookie('_sfdid') ?? ($cookieData['device_id'] ?? null),
            'user' => $request->cookie('_sfud') ? json_decode($request->cookie('_sfud'), true) : ($cookieData['user'] ?? ($user ?? null)),
            'utm' => [
                'source' => $request->get('utm_source') ?? ($cookieData['utm']['source'] ?? null),
                'medium' => $request->get('utm_medium') ?? ($cookieData['utm']['medium'] ?? null),
                'campaign' => $request->get('utm_campaign') ?? ($cookieData['utm']['campaign'] ?? null),
            ],
        ]);



        // $metabasedata = new MetaBaseData();
        // $metabasedata->setFbc($request->cookie('_fbc'));
        // $metabasedata->setFbp($request->cookie('_fbp'));
        // $metabasedata->setClientUserAgent($request->header('User-Agent'));
        // $metabasedata->setClientIpAddress($request->ip());
        // $metabasedata->setDeviceId($request->cookie('_sfdid'));
        // $metabasedata->setUserData($request->cookie('_sfud'));
        // $request->attributes->set('master_dl', $masterData);
        // setcookie('master_dl', json_encode($masterData), time() + (86400 * 90), "/");
        $response = $next($request);

        return $response->withCookie(
            cookie(
                'master_dl',
                json_encode($masterData),
                60 * 24 * 90,
                null,
                null,
                true,
                true,
                false,
                'Lax'
            )
        );
    }
}
