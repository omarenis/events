<?php

namespace Omarenis\Events\Models;

class Event
{
    public int $id;
    public string $title;
    public string $description;
    public Date $startDate;
    public Date $endDate;
    public string $location;
    public string $url;

    public function __construct($id, $title, $description, $startDate, $endDate, $location, $url)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->location = $location;
        $this->url = $url;
    }
}
