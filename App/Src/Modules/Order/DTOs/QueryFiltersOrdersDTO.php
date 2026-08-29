<?php

namespace App\Src\Modules\Order\DTOs;

use InvalidArgumentException;
use App\Src\Core\DB\Connection;
use App\Database\Schemas\SchemaSQL as Schema;
use App\Src\Core\HTTP\Request;
use App\Logs\Logger;

class QueryFiltersOrdersDTO {
    public array $properties;

    public function __construct( array $properties ) {
        $this->properties = $properties;
    }

    public static function mount(Request $req): self {
        $urlQuery = $req->query();
        $res = [
            'page' => (int)$urlQuery['page'] ?? 1,
            'limit' => (int)$urlQuery['limit'] ?? 10,
            'status' => $urlQuery['status'] ?? null,
            'customerId' => $urlQuery['customerId'] ?? null
        ];

        return new self($res);
    }

    // getters
    public function offset() { return (($this->properties['page'] - 1) * $this->properties['limit']); }
    public function limit() { return $this->properties['limit']; }
    public function status() { return $this->properties['status']; }
    public function customerId() { return $this->properties['customerId']; }
}