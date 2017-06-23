<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Listener\Locator;

use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Listener\ListenerCollection;
use GpsLab\Domain\Event\Listener\ListenerInterface;

/**
 * The purpose of this interface is to connect EventListeners to their Event.
 * You can have multiple Locator if you want to have multiple EventBus.
 */
interface LocatorInterface
{
    /**
     * Get the list of every event listeners that want to be warn when the event specified in argument is published.
     *
     * @param EventInterface $event
     *
     * @return ListenerInterface[]|ListenerCollection
     */
    public function getListenersForEvent(EventInterface $event);

    /**
     * Get the list of every EventListener.
     * This might be useful for debug.
     *
     * @deprecated It will be removed in 2.0.
     *
     * @return ListenerInterface[]|ListenerCollection
     */
    public function getRegisteredEventListeners();
}
