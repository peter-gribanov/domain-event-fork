[![Latest Stable Version](https://img.shields.io/packagist/v/gpslab/domain-event.svg?maxAge=3600&label=stable)](https://packagist.org/packages/gpslab/domain-event)
[![Total Downloads](https://img.shields.io/packagist/dt/gpslab/domain-event.svg?maxAge=3600)](https://packagist.org/packages/gpslab/domain-event)
[![Build Status](https://img.shields.io/travis/gpslab/domain-event.svg?maxAge=3600)](https://travis-ci.org/gpslab/domain-event)
[![Coverage Status](https://img.shields.io/coveralls/gpslab/domain-event.svg?maxAge=3600)](https://coveralls.io/github/gpslab/domain-event?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/gpslab/domain-event.svg?maxAge=3600)](https://scrutinizer-ci.com/g/gpslab/domain-event/?branch=master)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/9c7460e6-51b0-4cc3-9e4c-47066634017b.svg?maxAge=3600&label=SLInsight)](https://insight.sensiolabs.com/projects/9c7460e6-51b0-4cc3-9e4c-47066634017b)
[![StyleCI](https://styleci.io/repos/69552555/shield?branch=master)](https://styleci.io/repos/69552555)
[![License](https://img.shields.io/github/license/gpslab/domain-event.svg?maxAge=3600)](https://github.com/gpslab/domain-event)

Domain event
============

Library to create the domain layer of your **DDD** application

## Base usage

Create a domain event

```php
use GpsLab\Domain\Event\EventInterface;

final class PurchaseOrderCreatedEvent implements EventInterface
{
    private $customer;

    private $create_at;

    public function __construct(Customer $customer, \DateTimeImmutable $create_at)
    {
        $this->customer = $customer;
        $this->create_at = clone $create_at;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function getCreateAt()
    {
        return clone $this->create_at;
    }
}
```

Raise your event

```php
use GpsLab\Domain\Event\Aggregator\AbstractAggregateEvents;

final class PurchaseOrder extends AbstractAggregateEventsRaiseInSelf
{
    private $customer;

    private $create_at;

    public function __construct(Customer $customer)
    {
        $this->raise(new PurchaseOrderCreatedEvent($customer, new \DateTimeImmutable()));
    }

    protected function onPurchaseOrderCreated(PurchaseOrderCreatedEvent $event)
    {
        $this->customer = $event->getCustomer();
        $this->create_at = $event->getCreateAt();
    }
}
```

Create listener

```php
use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Listener\AbstractSwitchListener;

class SendEmailOnPurchaseOrderCreated extends AbstractSwitchListener
{
    private $mailer;

    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    protected function handlePurchaseOrderCreated(PurchaseOrderCreatedEvent $event)
    {
        $this->mailer->send('recipient@example.com', sprintf(
            'Purchase order created at %s for customer #%s',
            $event->getCreateAt()->format('Y-m-d'),
            $event->getCustomer()->getId()
        ));
    }
}
```

Dispatch events

```php
use GpsLab\Domain\Event\Listener\Locator\NamedEventLocator;
use GpsLab\Domain\Event\NameResolver\EventClassLastPartResolver;
use GpsLab\Domain\Event\Bus\Bus;

// use last part of event class as event name
$resolver = new EventClassLastPartResolver();

// first the locator
$locator = new NamedEventLocator($resolver);
// you can use several listeners for one event and one listener for several events
$locator->register('PurchaseOrderCreated', new SendEmailOnPurchaseOrderCreated(/* $mailer */));

// then the event bus
$bus = new Bus($locator);

// do what you need to do on your Domain
$purchase_order = new PurchaseOrder(new Customer(1));

// this will clear the list of event in your AggregateEvents so an Event is trigger only once
$bus->pullAndPublish($purchase_order);
```

## Documentation

* [Installation](docs/installation.md)
* Usage
  * [Base usage](docs/usage/base.md)
  * [Raise events in self](docs/usage/raise_in_self.md)
  * Listener
    * [Switch listener](docs/usage/listener/switch.md)
    * Locator
      * [Voter locator](docs/usage/listener/locator/voter.md)
      * [Event class locator](docs/usage/listener/locator/event_class.md)
      * [Event class last part locator](docs/usage/listener/locator/event_class_last_part.md)
      * [Named event locator](docs/usage/listener/locator/named_event.md)
      * [Container aware locator](docs/usage/listener/locator/container_aware.md)
  * Queue
    * [Queue event bus](docs/usage/queue/bus.md)
* [License](docs/license.md)
