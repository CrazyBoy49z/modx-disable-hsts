<?php
/**
 * Disable HSTS by adding 'Strict-Transport-Security' with 'max-age=0' to HTTP response
 * only if a request made via HTTPS
 *
 * Event: OnWebPagePrerender
 *
 * @var modX $modx
 * @author shelbalart
 *
 * @package disablehsts
 */

if (!empty($_SERVER['HTTP_HTTPS'])) {
    if ($modx->event->name === 'OnWebPagePrerender') {
        header('Strict-Transport-Security: max-age=0; includeSubDomains; preload');
    }
}
