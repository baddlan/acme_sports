<?php

namespace Drupal\acme_nfl\Model;

use stdClass;

class NflTeamsDto {
    /** @var Team[] Collection of teams */
    public array $teams;

    /** @var object Labels of data columns */
    public object $columns;

    /** @var string A unique identifier of the request */
    public string $hash;

    public function __construct() {
        $this->teams = [];
        $this->columns = new stdClass();
    }
}
