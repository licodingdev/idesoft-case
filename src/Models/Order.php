<?php

namespace Models;

class Order extends BaseModel
{
    protected string $table = 'orders';

    public function findWithItems($orderId)
    {
        $stmt = $this->db->prepare("
            SELECT o.*, oi.*, p.name as product_name, p.category
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            WHERE o.id = :id
        ");
        $stmt->execute(['id' => $orderId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function create($customerId, $items)
    {
        try {
            $this->db->beginTransaction();

            
            $total = array_reduce($items, function($carry, $item) {
                return $carry + ($item['quantity'] * $item['unit_price']);
            }, 0);

           
            $stmt = $this->db->prepare("
                INSERT INTO orders (customer_id, total)
                VALUES (:customer_id, :total)
            ");
            $stmt->execute([
                'customer_id' => $customerId,
                'total' => $total
            ]);
            $orderId = $this->db->lastInsertId();

          
            foreach ($items as $item) {
                $stmt = $this->db->prepare("
                    INSERT INTO order_items (order_id, product_id, quantity, unit_price, total)
                    VALUES (:order_id, :product_id, :quantity, :unit_price, :total)
                ");
                $stmt->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price']
                ]);
            }

            $this->db->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        try {
            $this->db->beginTransaction();

            
            $stmt = $this->db->prepare("DELETE FROM order_items WHERE order_id = :id");
            $stmt->execute(['id' => $id]);

            
            $stmt = $this->db->prepare("DELETE FROM orders WHERE id = :id");
            $stmt->execute(['id' => $id]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}