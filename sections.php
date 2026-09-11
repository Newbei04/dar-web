<?php
// section.php: Helper functions to mimic @yield and @section

$sections = []; // Array to store the section content
$currentSection = null; // Track the currently active section

// Start a section (similar to @section in Blade)
function startSection($name)
{
    global $currentSection;
    global $sections;

    $currentSection = $name;
    ob_start(); // Start output buffering for this section
}

// End the section (similar to @endsection in Blade)
function endSection()
{
    global $currentSection;
    global $sections;

    if ($currentSection !== null) {
        $sections[$currentSection] = ob_get_clean(); // Store buffered content in the sections array
        $currentSection = null;
    }
}

// Output the section content (similar to @yield in Blade)
function yieldSection($name)
{
    global $sections;

    if (isset($sections[$name])) {
        echo $sections[$name]; // Output the section content if it exists
    }
}

function fnCheck()
{
    echo "This is function test";
}

// Auto-detect the app's absolute base URL (scheme://host/base-path) so the app
// works under any folder (dar-webv2, dar_webv2, web root, custom vhost) on any
// server without having to keep APP_URL in .env in sync with the deployment.
function base_url()
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    if ($scheme === 'http' && (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')) {
        $scheme = 'https';
    }
    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';

    $docRoot = str_replace('\\', '/', (string)realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
    $appRoot = str_replace('\\', '/', (string)realpath(__DIR__));
    $path = '';
    if ($docRoot !== '' && strpos($appRoot, $docRoot) === 0) {
        $path = substr($appRoot, strlen($docRoot));
    }
    $path = rtrim($path, '/');

    $base = $scheme . '://' . $host . $path;
    return $base;
}
