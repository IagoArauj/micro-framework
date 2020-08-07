<?php

namespace Core\Factory;

class ClassMaker
{
    /**
     * Return a new object controller from namespace App\Controllers\
     * or false if something went wrong
     * 
     * @param string $class
     * @return mixed
     */
    public static function newController(string $class)
    {
        try {
            $class = 'App\\Controllers\\' . $class;
            $obj = new $class;
        } catch (\Throwable $th) {
            $obj = false;
        }
        return $obj;
    }
}