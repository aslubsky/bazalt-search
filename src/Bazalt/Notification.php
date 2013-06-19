<?php

namespace Bazalt;

class Notification
{
    protected static $dispatcher;

    public static function createNewDispatcher()
    {
        if (!self::$dispatcher) {
            self::$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
        }
        return self::$dispatcher;
    }

    public static function dispatcher()
    {
        if (!self::$dispatcher) {
            self::createNewDispatcher();
        }
        return self::$dispatcher;
    }

    public static function dispatch($eventName, $target = null, $args = [])
    {
        return self::dispatcher()->dispatch($eventName, new \Symfony\Component\EventDispatcher\GenericEvent($target, $args));
    }

    public static function addListener($eventName, $callback)
    {
        return self::dispatcher()->addListener($eventName, $callback);
    }
}