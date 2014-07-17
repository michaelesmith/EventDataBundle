<?php

namespace MS\Bundle\EventDataBundle\EventListener;

use MS\EventData\EventDataManager;

class EventDataListener {

    protected $eventDataManager;

    public function __construct(EventDataManager $eventDataManager)
    {
        $this->eventDataManager = $eventDataManager;
    }

    public function flush($event)
    {
        $this->eventDataManager->flush();
    }

} 
