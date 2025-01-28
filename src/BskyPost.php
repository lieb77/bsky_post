<?php

declare(strict_types=1);

namespace Drupal\bsky_post;

use Symfony\Component\DependencyInjection\ContainerInterface;
use potibm\Bluesky\Exception\HttpStatusCodeException;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\bsky\PostServiceInterface;

class BskyPost
{
    protected $bsky_connector;
    protected $site;
        
    public function __construct(PostServiceInterface $bsky,
    							ConfigFactoryInterface $factory )
    {
        $this->bsky_connector = $bsky;
        $config = $factory->get('system.site');
        $this->site = $config->get('name');
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