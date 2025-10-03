<?php

// This file will contain all of your custom helper functions.

if (!function_exists('get_nav_link_classes')) {
    function get_nav_link_classes($active) {
        $base = 'flex items-center px-6 py-3 transition duration-150 ease-in-out';
        if ($active) {
            return $base . ' bg-indigo-800 text-white';
        }
        return $base . ' text-indigo-200 hover:bg-indigo-800 hover:text-white';
    };
}