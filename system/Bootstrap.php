<?php
// MIT License
// Copyright (c) 2023 Ayberk Dönmez
//
// Permission is hereby granted, free of charge, to any person obtaining a copy of this software
// and associated documentation files (the "Software"), to deal in the Software without restriction,
// including without limitation the rights to use, copy, modify, merge, publish, distribute,
// sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in all copies or
// substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
// BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
// IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
// WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
// OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.


/**
 * Defined application's config path.
 */
define('APP_CONFIG_PATH', 'app/Config/');

/**
 * Defined application's controller path.
 */
define('APP_CONTROLLER_PATH', 'app/Controllers/');

/**
 * Defined application's helper path.
 */
define('APP_HELPER_PATH', 'app/Helpers/');

/**
 * Defined application's library path.
 */
define('APP_LIBRARY_PATH', 'app/Libraries/');

/**
 * Defined application's model path.
 */
define('APP_MODEL_PATH', 'app/Models/');

/**
 * Defined application's view path.
 */
define('APP_VIEW_PATH', 'app/Views/');

/**
 * Environment mode in PHP refers to the different stages of development
 * that a PHP application goes through. The most common environment modes
 * are development, testing, and production. Development mode is for when
 * the application is being developed and is not ready for public use.
 * Testing mode is for when the application is being tested and is almost
 * ready for public use. Production mode is for when the application is
 * ready for public use and is in its final stage of development. Each
 * environment mode may have different configurations and settings,
 * such as database connections or error reporting.
 */
define('ENVIRONMENT', 'testing');

/**
 * All 'helper' files are called.
 */
$helperValid = ['app/Helpers/*_helper.php', 'system/Helpers/*_helper.php'];
foreach ($helperValid as $helpers) {
    foreach (glob($helpers) as $file) {
        if(file_exists($file)) {
            require_once($file);
        }
    }
}

/**
 * Autoloader file called.
 */
require_once('./Autoloader.php');
?>