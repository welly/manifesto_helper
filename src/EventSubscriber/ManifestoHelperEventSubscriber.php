<?php

namespace Drupal\manifesto_helper\EventSubscriber;

use Drupal\Core\Cache\Cache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ManifestoHelperEventSubscriber implements EventSubscriberInterface {

  /**
   * Performs block cache invalidation depending on a particular event, in this
   * case a simple page load.
   */
  public function invalidateBlockCache(GetResponseEvent $event) {
    Cache::invalidateTags(['weather_block']);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('invalidateBlockCache');

    return $events;
  }
}
