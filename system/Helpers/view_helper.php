<?php
function view(string $viewName, array $data = [], array $cache = [])
{
    $view = new BlueComet\View\View();
    return $view->view($viewName, $data, $cache);
}
?>