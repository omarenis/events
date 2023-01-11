<?php

namespace Omarenis\Events\Repositories;

use Omarenis\Events\Models\Event;

if (!empty($GLOBALS)) {
	/**
	 * @property mixed $wpdb
	 */
	class EventRepository
	{
		public function __construct()
		{
			$this->wpdb = $GLOBALS['wpdb'];
		}

		public function findAll(): array
		{
			$events = [];
			$results = $this->wpdb->get_results('select * from wordpress.events');
			if (!empty($results)) {
				foreach ($results as $row) {
					$events[] = new Event(
						$row->id,
						$row->title,
						$row->description,
						$row->startDate,
						$row->endDate,
						$row->location,
						$row->url
					);
				}
			}
			return $events;
		}
		function findById(int $id): ?Event {
			$row = $this->wpdb->select_row("select * from wordpress.events where id = %d", $id);
			if (!empty($row)) {
				return new Event(
					(int) $row->id,
					$row->title,
					$row->description,
					$row->startDate,
					$row->endDate,
					$row->location,
					$row->url
				);
			}
			return null;
		}

		function create(Event $event)
		{
			$this->wpdb->query($this->wpdb->prepare("INSERT INTO wordpress.events (title, description, startDate, endDate, location, url) values (%s, %s, %s, %s, %s, %s)"));
		}
	}
}
