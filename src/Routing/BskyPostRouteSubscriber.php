<?php

declare(strict_types=1);

namespace Drupal\bsky_post\Routing;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Drupal\node\Entity\NodeType;

/**
 * Subscriber for Custom Elements UI routes.
 */
class BskyPostRouteSubscriber extends RouteSubscriberBase
{

    /**
     * The entity type manager service.
     *
     * @var \Drupal\Core\Entity\EntityTypeManagerInterface
     */
    protected $entityTypeManager;

    /**
     * Constructs a RouteSubscriber object.
     *
     * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
     *   The entity type manager service.
     */
    public function __construct(EntityTypeManagerInterface $entity_type_manager)
    {
        $this->entityTypeManager = $entity_type_manager;
  
    }

    /**
     * {@inheritdoc}
     */
    protected function alterRoutes(RouteCollection $collection)
    {
  
        $node_types = \Drupal\node\Entity\NodeType::loadMultiple();
        $types = \Drupal::config('bsky_post.settings')->get('types');
   
        $route_name = 'bsky_post.tab';
        $route = $collection->get($route_name);
        $options = $route->getOptions();
        $options['parameters']['node']['bundle'] = $types;
        $route->setOptions($options);
    }
  
  
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        $events = parent::getSubscribedEvents();
        $events[RoutingEvents::ALTER] = ['onAlterRoutes', -120];
        return $events;
    }

  
} // End of class
  
  
  
  
  
  
