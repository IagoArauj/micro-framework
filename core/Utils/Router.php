<?php

namespace Core\Utils;

use Core\Exceptions\{CallbackFunctionException};

class Router
{
    /*
     * All routes that the server must not return a 404 status will be listed right here,
     * within the array with the method's name
    */
    private static $get = [];
    private static $post = [];
    private static $delete = [];
    private static $put = [];
    private static $patch = [];

    /**
     * Check if the callback function is a string or a Closure, and then,
     * execute a valid response to our request, or throw a exception
     * 
     * @param mixed $callback
     * @return mixed
     */
    private function callbackFilter($callback)
    {
        switch ($callback) {
            case is_callable($callback):
                return \Core\Factory\ClosureMaker::returnClosure($callback);
                break;
            
            case is_string($callback):
                $explodedCallback = explode('@', $callback);
                $classname = $explodedCallback[0];
                $action = $explodedCallback[1];
                
                $obj = \Core\Factory\ClassMaker::newController($classname);
                return $obj->$action();
                
                break;

            default:
                throw new CallbackFunctionException("Undefined callback. Must be a string or a closure", 1);
                break;
        }
        
    }

    /**
     * Add a new route and callback to GET method
     * 
     * @param string $path
     * @param mixed $callback
     */
    public static function get(string $path, $callback)
    {
        self::$get[] = [$path, $callback];
    }

    /**
     * Add a new route and callback to POST method
     * 
     * @param string $path
     * @param mixed $callback
     */
    public static function post(string $path, $callback)
    {
        self::$post[] = [$path, $callback];
    }

    /**
     * Add a new route and callback to DELETE method
     * 
     * @param string $path
     * @param mixed $callback
     */
    public static function delete(string $path, $callback)
    {
        self::$delete[] = [$path, $callback];
    }

    /**
     * Add a new route and callback to PUT method
     * 
     * @param string $path
     * @param mixed $callback
     */
    public static function put(string $path, $callback)
    {
        self::$put[] = [$path, $callback];
    }

    /**
     * Add a new route and callback to PATCH method
     * 
     * @param string $path 
     * @param mixed $callback
     */
    public static function patch(string $path, $callback)
    {
        self::$patch[] = [$path, $callback];
    }

    /**
     * Search for the URI in the routes array
     * 
     * @param mixed $route 
     * @param string $uri
     * 
     * @return mixed[]
     */
    private static function searchUri($routes, $uri)
    {
        $found = false;
        $callback = '';
        foreach($routes as $route)
        {
            $routeArray = explode('/', $route[0]);
            $uriArray = explode('/', $uri);
            
            $param = [];

            for ($i = 0; $i < count($routeArray); $i++)
            {
                if(count($uriArray) === count($routeArray) && strpos($routeArray[$i], ':'))
                {
                    $param[] = $uriArray;
                    echo "param found";
                    $routeArray[$i] = $uriArray[$i];
                    
                    continue;
                }
                $route[0] = implode($routeArray, '/');
            }

            if($route[0] === $uri)
            {
                $found = true;
                $callback = $route[1];
                break;
            }
        }

        return [$found, $callback];
    }

    /**
     * Will get the current URI and check if this is listed in one of array's route.
     * Also, will return the response if the route was founded, or 404 HTTP's code
     * 
     * @param void
     * @return void
     */
    public static function on()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        
        switch ($method)
        {
            case "get":
                $checkedUri = self::searchUri(self::$get, $uri);

                if($checkedUri[0])
                {
                    return self::callbackFilter($checkedUri[1]);
                }
                return http_response_code(404);

                break;

            case "post":
                $checkedUri = self::searchUri(self::$post, $uri);
                
                if($checkedUri[0])
                {
                    return self::callbackFilter($checkedUri[1]);
                }
                return http_response_code(404);
                

                break;

            case "delete":
                $checkedUri = self::searchUri(self::$delete, $uri);
                
                if($checkedUri[0])
                {
                    return self::callbackFilter($checkedUri[1]);
                }
                return http_response_code(404);
                

                break;

            case "put":
                $checkedUri = self::searchUri(self::$put, $uri);
                
                if($checkedUri[0])
                {
                    return self::callbackFilter($checkedUri[1]);
                }
                return http_response_code(404);
                

                break;

            case "patch":
                $checkedUri = self::searchUri(self::$patch, $uri);
                
                if($checkedUri[0])
                {
                    return self::callbackFilter($checkedUri[1]);
                }
                return http_response_code(404);
                

                break;

            default:
                throw new \Exception("Method not allowed", 0);
                break;
        }
    }
}