<?php
declare(strict_types=1);

namespace Drupal\bsky_post\Hook;

use Drupal\Core\Url;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Hook\Attribute\Hook;

class BskyPostHooks {
	
	/**
     * Implements hook_help().
     *
     */
	#[Hook('help')]
	public function help($route_name, RouteMatchInterface $route_match) {
    	switch ($route_name) {
      		case 'help.page.bsky_post':
				$output = "<h2>Bluesky Post Help</h2>";
				return $output;
		}
	}

} //end of class