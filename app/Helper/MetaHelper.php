<?php

namespace App\Helper;

use Carbon\Carbon;
use Illuminate\Support\Facades\Request;

class MetaHelper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    private static function calculateSubdomainIndex(): int
    {
        // 1. Get the current hostname from the request (e.g., 'www.example.com')
        $hostname = Request::getHost();

        // 2. Remove the port number if present (e.g., 'localhost:8000' -> 'localhost')
        if (str_contains($hostname, ':')) {
            $hostname = explode(':', $hostname)[0];
        }

        // 3. Remove the TLD (Top-Level Domain) and one dot to get the components.
        // This is a simplified method. For true accuracy across all TLDs (like .co.uk),
        // a complex TLD list checker is needed. For common domains, this works.
        // Count the number of dots ('.') and add 1, then subtract the primary TLD part.

        // A simpler, more reliable method is counting segments and subtracting the TLD segment.
        $parts = explode('.', $hostname);
        $count = count($parts);

        // Assume the TLD (like .com, .net, etc.) is the last segment.
        // Index is the count of segments BEFORE the TLD.

        // Example: www.example.com -> ['www', 'example', 'com']. Count = 3.
        // SubdomainIndex = 3 - 1 (TLD) = 2.

        // Example: example.com -> ['example', 'com']. Count = 2.
        // SubdomainIndex = 2 - 1 (TLD) = 1.

        // Ensure index is at least 1 (for single-part domains like 'localhost' or 'example.com')
        return max(1, $count - 1);
    }

    /**
     * Generates the full formatted fbc string: fb.INDEX.creationTime.fbclid.Bg
     *
     * @param string $fbclid_value The raw fbclid from the URL.
     * @return string The correctly formatted fbc string.
     */
    public static function format_new_fbc(string $fbclid_value): string
    {
        if (empty($fbclid_value) || is_null($fbclid_value) || $fbclid_value === '') {

            if (isset($_COOKIE['custom_fbc']) && !empty($_COOKIE['custom_fbc']) && !is_null($_COOKIE['custom_fbc']) && $_COOKIE['custom_fbc'] != '') {
                return (isset($_COOKIE['custom_fbc']) ? $_COOKIE['custom_fbc'] : '');
            }
            return (isset($_COOKIE['_fbc']) ? $_COOKIE['_fbc'] : '');
        }

        if (isset($_COOKIE['custom_fbc']) && !empty($_COOKIE['custom_fbc']) && !is_null($_COOKIE['custom_fbc']) && $_COOKIE['custom_fbc'] != '') {
            $cookie_fbc = $_COOKIE['custom_fbc'];
            $fbclid_id = explode('.', $cookie_fbc)[3];
            if ($fbclid_id == $fbclid_value) {
                return isset($_COOKIE['custom_fbc']) ? $_COOKIE['custom_fbc'] : '';
            }

        }
        $version = 'fb';

        // **AUTOMATICALLY DETERMINE SUBDOMAIN INDEX**
        $subdomainIndex = self::calculateSubdomainIndex();

        $creationTime = Carbon::now()->getPreciseTimestamp(3);
        $suffix = 'Bg';
        $generatedString = "{$version}.{$subdomainIndex}.{$creationTime}.{$fbclid_value}.{$suffix}";
        setcookie('custom_fbc', $generatedString, time() + (86400 * 90), '/');
        return $generatedString;
    }

}
