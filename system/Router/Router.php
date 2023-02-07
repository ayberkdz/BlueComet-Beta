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


namespace BlueComet\Router;

class Router
{
    /**
     *  Default controller.
     */
    public string $defaultController = '';

    /**
     * Default controllers method.
     */
    public string $defaultMethod = '';

    /**
     * It will be used to store all route information in the directory hierarchy.
     */
    public array $routes = [];

    /**
     * Special keys to retrieve values (GET request) from the URI.
     */
    public array $placeholder = [
        ':num[0-9]?'      => '([0-9]+)',
        ':alpha'          => '([a-zA-Z]+)',
        ':alphanum[0-9]?' => '([0-9a-zA-Z]+)',
        ':any[0-9]?'      => '([0-9a-zA-Z-_]+)'
    ];

    /**
     * If it is desired to group the routes, a custom prefix value will be used.
     */
    public string $prefix = '';

    /**
     * If a GET request is to be used (usually this is used), the value of the first
     * parameter carries the page name. The second parameter can be used as a function
     * or a method from the controller class.
     */
    public function get(string $path, string|callable $callback): Router
    {
        $this->routes['get'][$this->prefix . $path] = [
            'callback' => $callback
        ];

        return $this;
    }

    /**
     * Use this method if a POST request is to be used.
     */
    public function post(string $path, string|callable $callback): void
    {
        $this->routes['post'][$path] = [
            'callback' => $callback
        ];
    }

    /**
     * If you are using long URIs, give them a name and simplify your work.
     */
    public function name(string $name): void
    {
        $key = array_key_last($this->routes['get']);
        $this->routes['get'][$key]['name'] = $name;
    }

    /**
     * You can use this function to use the URIs with the aliases you have given.
     */
    public function url(string $name, array $params = []): string
    {
        
        $route = array_key_first(array_filter($this->routes['get'], function($route) use ($name) {
            if(isset($route['name'])) {
                return $route['name'] === $name;
            }
        }));

        return str_replace(array_keys($params), array_values($params), $route);
    }

    /**
     * If multiple pages of yours are passing through a common directory,
     * you can group them. The first parameter should be the group name and
     * the second parameter should be a function.
     */
    public function group(string $prefix, \Closure $closure)
    {
        $this->prefix = $prefix;
        $closure();
        $this->prefix = '';
    }

    /**
     * If you want to add placeholders yourself, you can use this method.
     */
    public function where(string $key, string $pattern)
    {
        $this->placeholder[':' . $key] = '(' . $pattern . ')';
    }

    /**
     * If you want to make a redirect, use this method. The first parameter
     * is the name of the page you want to redirect to, and the second parameter
     * determines the name of the page where you want to redirect to.
     */
    public function redirect(string $from, string $to, int $status = 301)
    {
        $this->routes['get'][$from] = [
            'redirect' => $to,
            'status' => $status
        ];
    }

    /**
     * Set default controller.
     */
    public function setDefaultController(string $controller): string
    {
        return $this->defaultController = $controller;
    }

    /**
     * Set default controllers method.
     */
    public function setDefaultMethod(string $method): string
    {
        return $this->defaultMethod = $method;
    }

    /**
     * Get default page.
     */
    private function getDefaultPage(): void
    {
        $this->get('/', "$this->defaultController::$this->defaultMethod");
    }

    /**
     * The information in the $routes directory is processed through the
     * filter of the operation based on its hierarchy.
     */
    public function load(): void
    {
        $hasPage = false;
        $currentURI = ENVIRONMENT !== 'testing' ? $_SERVER['REQUEST_URI'] : str_replace('/' . explode('/',$_SERVER['REQUEST_URI'])[1], '', $_SERVER['REQUEST_URI']);

        $this->getDefaultPage();

        foreach ($this->routes[strtolower($_SERVER['REQUEST_METHOD'])] as $path => $props) {
            foreach ($this->placeholder as $key => $pattern) {
                $path = preg_replace('#' . $key .'#', $pattern, $path);
            }
            
            $pattern = '#^' . $path .'$#';
            if(preg_match($pattern, $currentURI, $params)) {

                $hasPage = true;
                array_shift($params);

                if(isset($props['redirect'])) {
                    $redirectURI = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];

                    if(ENVIRONMENT === 'testing') {
                        $redirectURI = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . explode('/',$_SERVER['REQUEST_URI'])[1];
                    }
                    if($props['redirect'] !== '/' || $props['redirect'] !== '') {
                        $redirectURI .= $props['redirect'];
                    }

                    header('Location:' . $redirectURI, true, $props['status']);
                    exit();
                }

                $callback = $props['callback'];

                if(is_callable($callback)) {
                    echo call_user_func_array($callback, $params);
                }
                elseif(is_string($callback)) {
                    [$controllerName, $controllerMethod] = explode('::', $callback);

                    $controllerName = '\BlueComet\Controllers\\' .  $controllerName;
                    $controllerClass = new $controllerName();
                    echo call_user_func_array([$controllerClass, $controllerMethod], $params);
                }
            }
        }

        if($hasPage === false) {
            // You can add a 404 page here if you'd like.
            die("Page Not Found");
        }
    }
}
?>