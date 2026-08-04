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
