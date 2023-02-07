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


/*
 | An autoloader class is a special class in PHP that is responsible for automatically
 | including the necessary files for a class when it is instantiated. The idea behind
 | an autoloader is to eliminate the need for manual inclusion of files in your application.
 | Instead, the autoloader is automatically registered when the application starts, and 
 | it takes care of including the necessary files for any class that is used in the application.
 */
spl_autoload_register(function ($class) {

    $prefix = 'BlueComet\\';
    
    $validDirectory = ['app', 'system'];
    foreach($validDirectory as $value) {

        $base_dir = __DIR__ . '/'. $value .'/';

        $len_prefix = strlen($prefix);
        if( strncmp($prefix, $class, $len_prefix) !== 0 ) {
            return;
        }
        
        $relative_class = substr($class, $len_prefix);

        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if( file_exists($file) ) {
            require_once($file);
        }
    }
});
?>