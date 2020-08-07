<?php

namespace Core\Factory;

class ClosureMaker
{   
    /**
     * Return the closure given or false if something went wrong
     * 
     * @param callable $closure
     * @return mixed
     */
    public static function returnClosure(callable $closure)
    {
        try {
            return call_user_func($closure);
        } catch (\Throwable $th) {
            return false;
        }
    }
}