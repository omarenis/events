<?php
/**
 * Plugin Name:     events
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     this is for events crud
 * Author:          omarenis
 * Author URI:      YOUR SITE HERE
 * Text Domain:     events
 * Domain Path:     /events
 * Version:         0.1.0
 * @package         Events
 */
declare(strict_types=1);

namespace Events;


use InvalidArgumentException;
use Omarenis\Events\Fields;
use Exception;
use Geniem\ACF\Exception as ExceptionAlias;
use Geniem\ACF\Group;
use RuntimeException;
require 'vendor/autoload.php';
/**
 * @throws Exception
 */
function loadDatabase(): void
{
	$wpdb = $GLOBALS['wpdb'];
	$sql = "
CREATE TABLE IF NOT EXISTS events_plugin_events (
    id INTEGER PRIMARY KEY auto_increment,
    title  TEXT,
    description TEXT,
    image TEXT,
    datetime_start DATETIME,
    datetime_end DATETIME
);
";
	if (!$wpdb->query($sql)) {
		throw new InvalidArgumentException("database creation error");
	}
}

register_activation_hook(__FILE__, 'loadDatabase');

try {
	$fields = new Fields();
	var_dump($fields);
	$formGroup = new Group('Events form');
	$formGroup->set_key('Event form');
	$formGroup->set_title('Event form for crud');
	$title = $fields->createTextField("title", "title", "title", "type title here");
	$description = $fields->createTextField("description", "description", "description", "type your description here", "TextArea");
	$formGroup->add_field($title);
	$formGroup->add_field($description);
} catch (ExceptionAlias $e) {
	var_dump($e);
}
