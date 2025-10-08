<?php

/*
 |--------------------------------------------------------------------------
 | ERROR DISPLAY
 |--------------------------------------------------------------------------
 | Don't show ANY in production environments. Instead, let the system catch
 | it and display a generic error message.
 */
ini_set('display_errors', '1');
error_reporting(E_ALL);

/*
 |--------------------------------------------------------------------------
 | DEBUG BACKTRACES
 |--------------------------------------------------------------------------
 | If true, this constant will tell the error screens to display debug
 | backtraces along with the other error information. If you would
 | prefer to not see this, set this value to false.
 */
defined('SHOW_DEBUG_BACKTRACE') || define('SHOW_DEBUG_BACKTRACE', true);

/*
 |--------------------------------------------------------------------------
 | DEBUG MODE
 |--------------------------------------------------------------------------
 | Debug mode is an experimental flag that can allow changes throughout
 | the system. It's not widely used currently, and may not survive
 | release of the framework.
 */
defined('CI_DEBUG') || define('CI_DEBUG', true);

/*
 |--------------------------------------------------------------------------
 | WORKAROUND: Locale Class for missing intl extension
 |--------------------------------------------------------------------------
 | If the intl extension is not available, create a simple mock
 */
if (!class_exists('Locale')) {
    class Locale {
        public static function setDefault($locale) {
            // Mock implementation - do nothing
            return true;
        }
        
        public static function getDefault() {
            return 'de';
        }
    }
}