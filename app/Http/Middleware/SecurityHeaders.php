<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds baseline HTTP security headers to every response (OWASP A05 / A02).
 *
 * The Content-Security-Policy ships in report-only mode by default because the
 * Filament panel and Livewire depend on inline scripts; enforcing it outright
 * would break them. Values are driven by config/security-headers.php.
 */
class SecurityHeaders
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', $this->string('security-headers.content_type_options', 'nosniff'));
        $response->headers->set('X-Frame-Options', $this->string('security-headers.frame_options', 'DENY'));
        $response->headers->set('Referrer-Policy', $this->string('security-headers.referrer_policy', 'strict-origin-when-cross-origin'));

        if (config('security-headers.hsts.enabled')) {
            $response->headers->set('Strict-Transport-Security', $this->hstsValue());
        }

        if (config('security-headers.csp.enabled')) {
            $policy = $this->string('security-headers.csp.policy', '');

            if ($policy !== '') {
                $header = config('security-headers.csp.report_only')
                    ? 'Content-Security-Policy-Report-Only'
                    : 'Content-Security-Policy';

                $response->headers->set($header, $policy);
            }
        }

        return $response;
    }

    private function hstsValue(): string
    {
        $configured = config('security-headers.hsts.max_age', 31536000);
        $maxAge = is_numeric($configured) ? (int) $configured : 31536000;
        $value = "max-age={$maxAge}";

        if (config('security-headers.hsts.include_subdomains')) {
            $value .= '; includeSubDomains';
        }

        if (config('security-headers.hsts.preload')) {
            $value .= '; preload';
        }

        return $value;
    }

    private function string(string $key, string $default): string
    {
        $value = config($key, $default);

        return is_string($value) ? $value : $default;
    }
}
