<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Freterapido_Helpers {
    static function fix_zip_code($zip) {
        $fixed = preg_replace('([^0-9])', '', $zip);

        return $fixed;
    }
}
