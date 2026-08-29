<?php
namespace App\Src\Modules\Order\Objects;

class OrderList {
    private array $list;

    public function __construct(array $data) {
        $this->list = $this->agrupItems($data);
    }

    // getters
    public function list() { return array_values($this->list); }

    public function agrupItems(array $data) {
        $res = [];
        foreach ($data as $row) {
            if (!isset($res[$row['order_id']])) {
                $res[$row['order_id']] = [
                    'orderId'   => (int)$row['order_id'],
                    'customerId'   => (int)$row['customer_id'],
                    'status'    => $row['status'],
                    'total'     => round($row['total'], 2),
                    'items'     => [[
                        'productId' => (int)$row['productId'],
                        'qty'       => (int)$row['qty'],
                        'price'     => round($row['price'], 2)
                    ]]
                ];
            } else {
                $res[$row['order_id']]['items'][] = [
                    'productId' => (int)$row['productId'],
                    'qty'       => (int)$row['qty'],
                    'price'     => round($row['price'], 2)
                ];
            }
        }
        return $res;
    }
}