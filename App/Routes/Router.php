<?php
namespace App\Routes;

class Router
{
    private $http;
    private $controller;
    private $url;
    private $method;
    private $params;
    private $namespace;

    public function __construct($http, $request, $namespace = null)
    {
        $this->http = $http;
        $this->url = explode('/', $request['url']);
        $this->method = 'index';
        $this->params = ['id' => 0];
        $this->namespace = ($namespace !== null) ? $namespace : '\App\Api\Controllers\\';
        $this->load();
    }

    public function load()
    {
        $prefix = strtolower(array_shift($this->url));

        if (isset($this->url[0]) && ($prefix == 'api')) {

            $this->controller = ucfirst($this->url[0]) . 'Controller';
            array_shift($this->url);

            if (isset($this->url[0])) {
                $this->params = $this->url;

                if (($this->http == 'GET') || $this->http == 'HEAD'):
                    $this->method = 'show';
                endif;

                if (($this->http == 'PUT') || $this->http == 'PATCH'):
                    $this->method = 'update';
                endif;

                if ($this->http == 'DELETE'):
                    $this->method = 'delete';
                endif;

            } else {
                if (($this->http == 'GET') || $this->http == 'HEAD'):
                    $this->method = 'index';
                endif;

                if ($this->http == 'POST'):
                    $this->method = 'store';
                endif;
            }
        } else {

            $this->controller = ucfirst($prefix) . 'Controller';

            if (isset($this->url[0])) {
                $this->method = $this->url[0];
                array_shift($this->url);

                if (isset($this->url[0])) {
                    $this->params = $this->url[0];
                }
            }
        }
    }

    public function run()
    {
        if (class_exists($this->namespace . $this->controller) && method_exists($this->namespace . $this->controller, $this->method)) {
            try {
                $response = call_user_func_array(array($this->namespace . $this->controller, $this->method), $this->params);
                return $response;
            } catch (\Exception $e) {
                return json_encode(['success' => false, 'message' => $e->getMessage()], http_response_code(500));
            }
        } else {
            return json_encode(['success' => false, 'message' => 'Not Found'], http_response_code(404));
        }
    }
}
