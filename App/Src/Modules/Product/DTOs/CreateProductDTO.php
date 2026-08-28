<?php

namespace App\Src\Modules\Product\DTOs;

use InvalidArgumentException;

class CreateProductDTO
{
    public readonly string $name;
    public readonly float $price;
    public readonly int $stock;

    public function __construct( string $name, float $price, int $stock ) {
        $this->name     = $name;
        $this->price    = $price;
        $this->stock    = $stock;
    }

    /**
     * Transform array to DTO and validate keys
     * params:
     *  - data <array> => Key required [name, price and stock]
     * Throw:
     *  - InvalidArgumentExceptions
     */
    public static function fromArray(array $data): self {
        // Validate all params are present
        if ( !isset($data['name']) || !isset($data['price']) || !isset($data['stock']) ) {
            throw new InvalidArgumentException( 'name, price and stock are required', 422 );
        }

        // Validate name rules
        if (!is_string($data['name']) || trim($data['name']) === '') {
            throw new InvalidArgumentException( 'name must be a valid string', 400 );
        }

        // Validate price be over or equal 0 and is numeric
        if (!is_numeric($data['price']) || $data['price'] < 0) {
            throw new InvalidArgumentException( 'price must be a valid positive number', 400 );
        }

        // Validate stock as integer value and over or equal 0
        if (!is_integer($data['stock']) || $data['stock'] < 0) {
            throw new InvalidArgumentException( 'stock must be a valid integer and positive number', 400 );
        }

        return new self( name: trim($data['name']), price: (float) $data['price'], stock: (int) $data['stock'] );
    }

    /**
     * Get array to inserts
     */
    public function data(): array {
        return [
            'name' => sprintf('"%s"', $this->name),
            'price' => round($this->price, 2),
            'stock' => $this->stock,
        ];
    }
}