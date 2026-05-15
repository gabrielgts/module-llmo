<?php declare(strict_types=1);

namespace Gtstudio\Llmo\Model\Validator;

/**
 * Validates that a URL is a well-formed HTTPS address.
 *
 * Enforces the `https` scheme only — `http://`, `javascript:`, `data:`,
 * and any other scheme are rejected. Used for Campaign target URLs to
 * prevent open-redirect and injection attacks (security finding H3).
 */
class UrlValidator
{
    /**
     * RFC-3986-compatible HTTPS URL pattern.
     *
     * Covers host, optional port, path, query string, and fragment while
     * rejecting every scheme other than `https`.
     */
    private const HTTPS_PATTERN =
        '#^https://'
        . '[a-zA-Z0-9\-._~%!$&\'()*+,;=:@/?\[\]#]+'
        . '$#';

    /**
     * Check whether the given URL is a valid HTTPS address.
     *
     * @param string $url
     * @return bool
     */
    public function isValid(string $url): bool
    {
        if ($url === '') {
            return false;
        }

        if (\stripos($url, 'https://') !== 0) {
            return false;
        }

        if (!\preg_match(self::HTTPS_PATTERN, $url)) {
            return false;
        }

        // Ensure a host exists after the scheme — rejects `https://` alone.
        $withoutScheme = \substr($url, 8);
        $host = \strstr($withoutScheme, '/', true);
        $host = $host === false ? $withoutScheme : $host;

        return $host !== '';
    }
}
