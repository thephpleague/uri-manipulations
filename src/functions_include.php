<?php

// Don't redefine the functions if included multiple times.
if (!function_exists('League\Uri\normalize')) {
    require __DIR__.'/Modifiers/functions.php';
    require __DIR__.'/functions.php';
}
