<?php

namespace Controllers;

use Models\Order;
use Models\Product;

class OrderController extends BaseController
{
    private $orderModel;
    private $productModel;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->productModel = new Product();
    }

    public function index()
    {
        return $this->jsonResponse($this->orderModel->findAll());
    }

    public function show($id)
    {
        $order = $this->orderModel->findWithItems($id);
        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }
        return $this->jsonResponse($order);
    }

    public function create()
    {
        $data = $this->getRequestBody();
        
        // Base validation
        if (!isset($data['customerId']) || !isset($data['items']) || empty($data['items'])) {
            return $this->errorResponse('Invalid request data. Required fields: customerId and items array', 400);
        }

        // Validate items structure
        foreach ($data['items'] as $index => $item) {
            $requiredFields = ['productId', 'quantity', 'unitPrice'];
            foreach ($requiredFields as $field) {
                if (!isset($item[$field])) {
                    return $this->errorResponse(
                        sprintf('Invalid item data at index %d. Missing required field: %s', $index, $field),
                        400
                    );
                }
            }

            // Validate quantity
            if (!is_numeric($item['quantity']) || $item['quantity'] <= 0) {
                return $this->errorResponse(
                    sprintf('Invalid quantity at index %d. Quantity must be a positive number', $index),
                    400
                );
            }

            // Validate unit price
            if (!is_numeric($item['unitPrice']) || $item['unitPrice'] <= 0) {
                return $this->errorResponse(
                    sprintf('Invalid unit price at index %d. Unit price must be a positive number', $index),
                    400
                );
            }

            // Validate stock
            $product = $this->productModel->findById($item['productId']);
            if (!$product) {
                return $this->errorResponse(
                    sprintf('Product not found with ID: %s', $item['productId']),
                    404
                );
            }
            if ($product['stock'] < $item['quantity']) {
                return $this->errorResponse(
                    sprintf('Insufficient stock for product: %s. Available: %d, Requested: %d', 
                        $product['name'], 
                        $product['stock'], 
                        $item['quantity']
                    ),
                    400
                );
            }
        }

        try {
            $items = array_map(function($item) {
                return [
                    'product_id' => $item['productId'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unitPrice']
                ];
            }, $data['items']);

            $orderId = $this->orderModel->create($data['customerId'], $items);
            return $this->jsonResponse(['id' => $orderId], 201);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while creating the order: ' . $e->getMessage(), 500);
        }
    }

    public function delete($id)
    {
        try {
            $order = $this->orderModel->findById($id);
            if (!$order) {
                return $this->errorResponse('Order not found', 404);
            }

            $this->orderModel->delete($id);
            return $this->jsonResponse(['message' => 'Order deleted successfully'], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}