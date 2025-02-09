<?php

namespace Controllers;

use Models\Order;

class DiscountController extends BaseController
{
    private $orderModel;

    public function __construct()
    {
        $this->orderModel = new Order();
    }

    public function calculate($orderId)
    {
        $order = $this->orderModel->findWithItems($orderId);
        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        $discounts = [];
        $total = $order[0]['total']; 
        $subtotal = $total;

       
        if ($total >= 1000) {
            $discountAmount = $total * 0.10;
            $subtotal -= $discountAmount;
            $discounts[] = [
                'discountReason' => '10_PERCENT_OVER_1000',
                'discountAmount' => number_format($discountAmount, 2, '.', ''),
                'subtotal' => number_format($subtotal, 2, '.', '')
            ];
        }

       
        $categoryTwoItems = array_filter($order, function($item) {
            return $item['category'] == 2;
        });

        $totalCategoryTwoQuantity = array_reduce($categoryTwoItems, function($carry, $item) {
            return $carry + $item['quantity'];
        }, 0);

        if ($totalCategoryTwoQuantity >= 6) {
            $freeItemCount = floor($totalCategoryTwoQuantity / 6);
            $categoryTwoUnitPrice = $categoryTwoItems[0]['unit_price'];
            $discountAmount = $freeItemCount * $categoryTwoUnitPrice;
            $subtotal -= $discountAmount;
            $discounts[] = [
                'discountReason' => 'BUY_5_GET_1',
                'discountAmount' => number_format($discountAmount, 2, '.', ''),
                'subtotal' => number_format($subtotal, 2, '.', '')
            ];
        }

        $categoryOneItems = array_filter($order, function($item) {
            return $item['category'] == 1;
        });

        if (count($categoryOneItems) >= 2) {
            $cheapestItem = array_reduce($categoryOneItems, function($carry, $item) {
                if (!$carry || $item['unit_price'] < $carry['unit_price']) {
                    return $item;
                }
                return $carry;
            });

            $discountAmount = $cheapestItem['unit_price'] * 0.20;
            $subtotal -= $discountAmount;
            $discounts[] = [
                'discountReason' => 'CHEAPEST_CATEGORY_ONE_20_PERCENT',
                'discountAmount' => number_format($discountAmount, 2, '.', ''),
                'subtotal' => number_format($subtotal, 2, '.', '')
            ];
        }

        $totalDiscount = $total - $subtotal;

        return $this->jsonResponse([
            'orderId' => $orderId,
            'discounts' => $discounts,
            'totalDiscount' => number_format($totalDiscount, 2, '.', ''),
            'discountedTotal' => number_format($subtotal, 2, '.', '')
        ]);
    }
}