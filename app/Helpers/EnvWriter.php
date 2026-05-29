<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Artisan;

class EnvWriter
{
    /**
     * Write multiple key=value pairs to the .env file, then clear config cache.
     *
     * @param array<string,string> $values  ['KEY' => 'value', ...]
     */
    public static function set(array $values): void
    {
        $envPath = base_path('.env');
        $content = file_get_contents($envPath);

        foreach ($values as $key => $value) {
            $key   = strtoupper($key);
            $value = self::formatValue((string) ($value ?? ''));

            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            } else {
                $content .= PHP_EOL . "{$key}={$value}";
            }
        }

        file_put_contents($envPath, $content);

        Artisan::call('config:clear');
    }

    private static function formatValue(?string $value): string
    {
        // Wrap in quotes if value contains spaces or is empty
        if ($value === '' || str_contains($value, ' ')) {
            return '"' . addslashes($value) . '"';
        }
        return $value;
    }
}
