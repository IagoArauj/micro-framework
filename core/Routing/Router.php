<?php

namespace Core\Routing;

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
     * Check if the callback function is a string or a Closure
     * 
     * @param mixed $callback
     * @return Closure
     */
    private function callbackFilter($callback)
    {
        switch ($callback) {
            case is_callable($callback):
                return call_user_func($callback);
                break;
            
            case is_string($callback):
                // If the callback is in a function, then the string will be Class@method, so:
                $temp = explode('@', $callback);
                
                $classname = '\\App\\Controllers\\' . $temp[0];
                $action = $temp[1];
                
                $obj = new $classname;
                return $obj->$action();
                
                break;
            default:
                throw new CallbackFunctionException("Undefined callback. Must be a string or a closure", 1);
                break;
        }
        
    }

    /**
     * Add a new route listening with GET method
     * 
     * @param string $path
     * @param mixed $callback
     */
    public static function get(string $path, $callback)
    {
        self::$get[] = [$path, $callback];
    }

    /**
     * Add a new route listening with POST method
     * 
     * @param string $path
     * @param mixed $callback
     */
    public static function post(string $path, $callback)
    {
        self::$post[] = [$path, $callback];
    }

    /**
     * Add a new route listening with DELETE method
     * 
     * @param string $path
     * @param mixed $callback
     */
    public static function delete(string $path, $callback)
    {
        self::$delete[] = [$path, $callback];
    }

    /**
     * Add a new route listening with PUT method
     * 
     * @param string $path
     * @param mixed $callback
     */
    public static function put(string $path, $callback)
    {
        self::$put[] = [$path, $callback];
    }

    /**
     * Add a new route listening with PATCH method
     * 
     * @param string $path 
     * @param mixed $callback
     */
    public static function patch(string $path, $callback)
    {
        self::$patch[] = [$path, $callback];
    }

    /**
     * Search for the URI in a routes array
     * 
     * @param mixed $route 
     * @param string $uri
     * 
     * @return Closure
     */
    private static function searchUri($routes, $uri)
    {
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
                return self::callbackFilter($route[1]);
            }
        }

        return http_response_code(404);
    }

    /**
     * Will get the current URI and check if this is listed in one of array's route
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
                self::searchUri(self::$get, $uri);

                break;

            case "post":
                self::searchUri(self::$post, $uri);
                break;

            case "delete":
                self::searchUri(self::$delete, $uri);
                break;

            case "put":
                self::searchUri(self::$put, $uri);
                break;

            case "patch":
                self::searchUri(self::$patch, $uri);
                break;

            default:
                throw new \Exception("Method not allowed", 0);
                break;
        }
    }
}