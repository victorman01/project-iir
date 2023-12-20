<?php

/**
 * @file
 * This file simply redirects to /demo. For demonstration purposes only.
 */

if (php_sapi_name() != "cli") {
  $url = $_SERVER['HTTP_HOST'];
  $www = strpos($url, 'www.');
  if ($www === 0) {
    // The request begins with "www." . Rewrite the URL only to include
    // everything after "www." and trigger the redirect.
    $url = substr($url, 4);
  }
  if (strpos($url, 'localhost') === FALSE) {
    // Send all traffic to HTTPS.
    header('HTTP/1.0 301 Moved Permanently');
    header('Location: ' . 'https://' . $url . $_SERVER['REQUEST_URI'] . 'demo');
    header('Cache-Control: public, max-age=3600');
    exit();
  }
}
