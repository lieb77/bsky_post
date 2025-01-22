<?php

declare(strict_types=1);

namespace Drupal\bsky_post;

use Symfony\Component\DependencyInjection\ContainerInterface;
use potibm\Bluesky\Exception\HttpStatusCodeException;
use Drupal\bsky\PostServiceInterface;

class BskyPost
{
    protected $bsky_connector;
    protected $site;
        
    public function __construct(PostServiceInterface $bsky )
    {
        $this->bsky_connector = $bsky;
        $config = \Drupal::config('system.site');
        $this->site = $config->get('name');
    }
    
    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        return new static(              
            $container->get('bsky.post_service')
        );
    }
    
    public function post($message, $link)
    {
        $post = $this->bsky_connector->createPost($message);
        $post = $this->bsky_connector->addCard($post, $link, $this->site, "Read the full post.");
                        
        try {
            $this->bsky_connector->sendPost($post);
        } 
        catch (HttpStatusCodeException $e) {
            return $e->getMessage();
        }
        return false;
    }    
    
}