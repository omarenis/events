<?php
namespace Omarenis\Events\Repositories;

$wpdb = $GLOBALS['wpdb'];
class EventRepository
{
    public function findAll($wpdb)
    {
        $wpdb->query("SELECT * FROM  wordpress.events");
    }
}
