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


namespace BlueComet\View;
class View
{
    /**
     * Responsible for rendering the user interface. It takes the data from the model,
     * processes it, and presents it to the user. The view is the portion of the
     * application that the user interacts with and is responsible for displaying
     * the data to the user.
     * 
     * @param string $viewName
     * @param array $data
     * @param array $cache
     * @return null|string
     */
    public function view(string $viewName, array $data = [], array $cache = [])
    {
        $viewName = pathinfo($viewName, PATHINFO_EXTENSION) ? $viewName : $viewName . '.php';

        if(! file_exists(APP_VIEW_PATH . $viewName)) {
            return exit('SYSTEM_ERROR: view() -> @param string $viewName Unknown appearance file called.');
        }

        foreach ($data as $key => $value) {
            if(! array_key_exists($key, $data)) {
                return exit('SYSTEM_ERROR: view() -> @param array $data The key has not been used within the array.');
            }
        }

        extract($data);
        
        if(! empty($cache)) {
            if(! array_key_exists('expires', $cache)) {
                return exit('SYSTEM_ERROR: view() -> @param array $cache Key for \'expires\' not found.');
            }
            if(! array_key_exists('cache_name', $cache)) {
                return exit('SYSTEM_ERROR: view() -> @param array $cache Key for \'cache_name\' not found.');
            }

            $cache['cache_name'] = pathinfo($cache['cache_name'], PATHINFO_EXTENSION) ? $cache['cache_name'] : $cache['cache_name'] . '.php';

            if (! is_dir(APP_VIEW_PATH . 'cache/')) {
                mkdir(APP_VIEW_PATH . 'cache/');
            }
            $cacheStore = APP_VIEW_PATH . 'cache/' . $cache['cache_name'];

            if(file_exists($cacheStore) && time() - $cache['expires'] < filemtime($cacheStore)) {
                readfile($cacheStore);
                exit();
            }
            else {
                if(file_exists($cacheStore)) {
                    unlink($cacheStore);
                }
                ob_start();
                require_once(APP_VIEW_PATH . $viewName);
                $reCache = fopen($cacheStore, 'w+');
                fwrite($reCache, ob_get_contents());
                fclose($reCache);
                ob_end_flush();
            }
        }
        else {
            ob_start();
            require_once(APP_VIEW_PATH . $viewName);
            ob_end_flush();
        }
    }
}
?>