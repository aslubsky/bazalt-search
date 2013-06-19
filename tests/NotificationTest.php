<?php

namespace tests;

use Bazalt\Notification;

class TestComponent implements \Bazalt\Notification\INotifiable
{
    const EVENT_REGISTRATION_COMPLETE = 'user.registration.complete';

    public static function getNotifications()
    {
        return [
            self::EVENT_REGISTRATION_COMPLETE => ''
        ];
    }

    public static function getSubscribedEvents()
    {
        return [];
    }
}

class NotificationTest extends \tests\BaseCase
{
    protected $view;

    protected function setUp()
    {
        Notification::createNewDispatcher();
    }

    protected function tearDown()
    {
    }

    public function testDispatch()
    {
        Notification::addListener('OnAdd', function ($event) {
            $this->assertEquals($event->getName(), 'OnAdd');
        });

        Notification::dispatch('OnAdd', $this, ['test']);


        /*$this->assertEquals(['test' => __DIR__ . DIRECTORY_SEPARATOR .  'templates'], $this->view->folders());

        $this->view->folders([
            'test'  => __DIR__ . DIRECTORY_SEPARATOR . 'templates',
            'test2' => __DIR__ . DIRECTORY_SEPARATOR . 'templates2'
        ]);

        $this->assertEquals([
            'test'  => __DIR__ . DIRECTORY_SEPARATOR . 'templates',
            'test2' => __DIR__ . DIRECTORY_SEPARATOR . 'templates2'
        ], $this->view->folders());*/
    }

    public function testINotifiable()
    {
        Notification::addListener(TestComponent::EVENT_REGISTRATION_COMPLETE, function ($event) {
            $this->assertEquals($event->getName(), TestComponent::EVENT_REGISTRATION_COMPLETE);
            $this->assertEquals(['test'], $event->getArguments());
            $this->assertSame($this, $event->getSubject());
        });

        Notification::dispatch(TestComponent::EVENT_REGISTRATION_COMPLETE, $this, ['test']);

    }
    /**
     * @expectedException Exception
     
    public function testFetchError()
    {
        //$this->assertEquals('-', $this->view->fetch('test-invalid'));
    }*/
}