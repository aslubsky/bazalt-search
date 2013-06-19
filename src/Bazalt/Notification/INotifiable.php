<?php

namespace Bazalt\Notification;

interface INotifiable extends \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    public static function getNotifications();
}