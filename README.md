README
======

What is it?
-------------------

This is a library that allows for integration of the EventData library (https://github.com/michaelesmith/EventData) by adding a panel to the web profiler

Installation
------------

### Use Composer (*recommended*)

Get Composer at http://getcomposer.org

    php composer.phar require "michaelesmith/event-data-bundle" "dev-master"

Examples
--------

Example configuration

```yaml
# app/config/config.yml
    events_storage:
        class: MS\EventData\Storage\KeenIO
        arguments: [ @keen_io ]

    events_logger:
        class: MS\EventData\Logger\PSRLogger
        arguments: [@logger, @events]

    events_logger_generic:
        class: MS\EventData\Logger\GenericLogger

    events_data_collector:
        class: MS\Bundle\EventDataBundle\DataCollector\EventDataCollector
        arguments: [@events, @events_logger_generic]
        tags:
            - { name: data_collector, template: "MSEventDataBundle:Collector:events", id: "event_data" }

    events_data_listener:
        class: MS\Bundle\EventDataBundle\EventListener\EventDataListener
        arguments: [@events]
        tags:
            -
                name: kernel.event_listener
#                event: kernel.finish_request
                event: kernel.terminate
                method: flush
                priority: 0

    events:
        class: MS\EventData\EventDataManager
        arguments: [@events_storage]
        calls:
            - [addLogger, [@events_logger]]
            - [addLogger, [@events_logger_generic]]
            - [setDebug, [%kernel.debug%]]
            - [setDelayed, ["@=not parameter('kernel.debug')"]]
```

To use the keen storage you will to require the keenIO bundle"keen-io/keen-io-bundle": "~1.0". This also configures a PSRLogger with the default logger instance. The generic logger is used by the profiler panel. The data collector is what powers the profiler panel. The event listener allows the events to be flushed to the storage after the response has been sent to the user to prevent a delay. For the data collector to have the response you need to use kernel.finish_request which is called just before the response is sent, in production you should use kernel.terminate which is called after the response is sent.

To use it in an action you can simple do:

```php
$this->container->get('events')->store(new Event('page_view', array('homepage')));
```

The event class passed to it can be anything that implements the \MS\EventData\Event\EventInterface or extends \MS\EventData\Event\Event. I have found it helpful to use my own Event class so it can automatically add data such as current time, user ip or logged in username.
