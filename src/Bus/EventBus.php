<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Bus;

use GpsLab\Domain\Event\Aggregator\AggregateEventsInterface;
use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Listener\ListenerCollection;
use GpsLab\Domain\Event\Listener\ListenerInterface;
use GpsLab\Domain\Event\Listener\Locator\LocatorInterface;

class EventBus implements EventBusInterface
{
    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @param LocatorInterface $locator
     */
    public function __construct(LocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * Publishes the event $event to every EventListener that wants to.
     *
     * @param EventInterface $event
     */
    public function publish(EventInterface $event)
    {
        foreach ($this->locator->getListenersForEvent($event) as $listener) {
            $listener->handle($event);
        }
    }

    /**
     * @param AggregateEventsInterface $aggregator
     */
    public function pullAndPublish(AggregateEventsInterface $aggregator)
    {
        foreach ($aggregator->pullEvents() as $event) {
            $this->publish($event);
        }
    }

    /**
     * @deprecated It will be removed in 2.0.
     *
     * @return ListenerInterface[]|ListenerCollection
     */
    public function getRegisteredEventListeners()
    {
        return $this->locator->getRegisteredEventListeners();
    }
}
