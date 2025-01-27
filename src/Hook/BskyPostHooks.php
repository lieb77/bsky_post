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
				$output  = "<h2>Bluesky Post Help</h2>";
				$output .= "<h3>Dependencies</h3>";
				$output .= "<p>You must have the BlueSky Integration module installed and configured. ";
				$output .= "The BlueSky Integration module is configured with your credentials ";
				$output .= "for the BlueSky social network.</p>";
				$output .= "<h3>Configuration</h3>";
				$output .= "<p>The settings form lets you choose which content types you want ";
				$output .= "to be available to share to Bluesky</p>";
				$output .= "<h3>Usage</h3>";
				$output .= "<p>Once configured the actions menu for the configured content types ";
				$output .= "will contain a 'Share to Bluesky' tab.</p>";
				$output .= "<p>This will bring up a form with the Title and Summary fields ";
				$output .= "from the content, as well as a link to it. You can edit these ";
				$output .= "fields as desired before posting to Bluesky.</p>";
				
				return $output;
		}
	}

} //end of class