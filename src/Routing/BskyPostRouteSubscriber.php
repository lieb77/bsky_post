<?php

declare(strict_types=1);

namespace Drupal\bsky_post\Routing;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\Routing\RouteCollection;

/**
 * Subscriber for Custom Elements UI routes.
 */
class BskyPostRouteSubscriber extends RouteSubscriberBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var Drupal\Core\Config\ConfigFactoryInterfac
   */
  protected $config;

  /**
   * Constructs a RouteSubscriber object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Config\ConfigFactoryInterfac
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    ConfigFactoryInterface $factory,
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->config = $factory->get('bsky_post.settings');
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Get the selected types from config.
    $types = $this->config->get('types');

    // Modify our route parameters->node->bundle
    // to include our selected node types.
    $route_name = 'bsky_post.tab';
    $route = $collection->get($route_name);
    $options = $route->getOptions();
    $options['parameters']['node']['bundle'] = $types;
    $route->setOptions($options);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    $events = parent::getSubscribedEvents();
    $events[RoutingEvents::ALTER] = ['onAlterRoutes', -120];
    return $events;
  }

} // End of class
