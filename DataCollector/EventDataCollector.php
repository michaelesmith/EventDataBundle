<?php

namespace MS\Bundle\EventDataBundle\DataCollector;

use MS\EventData\EventDataManager;
use MS\EventData\Logger\GenericLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class EventDataCollector extends DataCollector {

    protected $eventManager;

    protected $eventLogger;

    public function __construct(EventDataManager $eventManager, GenericLogger $eventLogger)
    {
        $this->eventManager = $eventManager;
        $this->eventLogger = $eventLogger;
    }

    /**
     * Collects data for the given Request and Response.
     *
     * @param Request $request A Request instance
     * @param Response $response A Response instance
     * @param \Exception $exception An Exception instance
     *
     * @api
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $loggers = array();
        foreach ($this->eventManager->getLoggers() as $logger) {
            $loggers[] = get_class($logger);
        }

        $this->data = array(
            'debug' => $this->eventManager->getDebug(),
            'delayed' => $this->eventManager->getDelayed(),
            'events' => $this->eventLogger->getEvents(),
            'storage' => get_class($this->eventManager->getStorage()),
            'loggers' => $loggers,
            'flushed' => $this->eventManager->hasFlushed(),
        );
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     *
     * @api
     */
    public function getName()
    {
        return 'event_data';
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param Object $object
     * @return string
     */
    public function getClass($object)
    {
        return get_class($object);
    }

} 
