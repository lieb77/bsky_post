<?php

declare(strict_types=1);

namespace Drupal\bsky_post;

use potibm\Bluesky\Exception\HttpStatusCodeException;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\bsky\PostServiceInterface;

/**
 * Provides the post function to post content to Bluesky.
 */
class BskyPost {

  /**
   * The Post service instance.
   *
   * @var Drupal\bsky\PostServiceInterface
   */
  protected $bskyConnector;

  /**
   * Site name .
   *
   * @var string
   */
  protected $site;

  /**
   * Constructor.
   *
   * @param \Drupal\bsky\PostServiceInterface $bsky
   *   The bsky post service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $factory
   *   The config factory.
   */
  public function __construct(
    PostServiceInterface $bsky,
    ConfigFactoryInterface $factory,
  ) {
    $this->bskyConnector = $bsky;
    $config = $factory->get('system.site');
    $this->site = $config->get('name');
  }

  /**
   * Post the message.
   *
   * @param string $message
   *   Holds a message.
   * @param string $link
   *   Holds a link.
   */
  public function post($message, $link) {
    $post = $this->bskyConnector->createPost($message);
    $post = $this->bskyConnector->addCard($post, $link, $this->site, "Read the full post.");

    try {
      $this->bskyConnector->sendPost($post);
    }
    catch (HttpStatusCodeException $e) {
      return $e->getMessage();
    }
    return FALSE;
  }

}
