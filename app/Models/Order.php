<?php

namespace App\Models;

use PHPFramework\Model;

use App\Entity\Users;
use App\Entity\Products;
use App\Entity\Orders;
use App\Entity\ProdOrders;

class Order extends Model
{
    /**
     * Рассчет общей суммы
     */
    public function calcTotalAmout(Array $products) {

        $total = 0;

        foreach ($products as $product) {
            $result = db()->entityManager()->getRepository(Products::class)
                ->createQueryBuilder('pr')
                ->select('pr.price, pr.availability')
                ->where('pr.id = :id')
                ->setParameter('id', $product['product_id'])
                ->getQuery()
                ->getOneOrNullResult();

            if (!$result || !$result['availability'] || $product['quantity'] <= 0) {
                return false;
            }
            
            $total += $result['price'] * $product['quantity'];
        }

        return (float) $total;
    }

    /**
     * Создание основного заказа
     */
    public function createMainOrder(int $userId, $total, $comment) {

        $userRef = db()->entityManager()->getReference(Users::class, $userId);

        $prodOrders = (new ProdOrders())
            ->setUser($userRef)
            ->setStatus('new')
            ->setPrice($total)
            ->setComment($comment);

        if (!$this->validate($prodOrders)) {
            throw new \InvalidArgumentException('Invalid category');
        }

        db()->entityManager()->persist($prodOrders);
        db()->entityManager()->flush();

        return $prodOrders;
    }

    /**
     * Создание позиций заказа
     */
    public function createItemOrder($products, $prodOrders) {

        foreach ($products as $product) {
            $productEntity = db()->entityManager()
                ->getRepository(Products::class)
                ->find($product['product_id']);

            if (!$productEntity) {
                throw new \Exception("Product not found: " . $product['product_id']);
            }

            $order = (new Orders())
                ->setProdOrders($prodOrders)
                ->setProduct($productEntity)
                ->setQuantity($product['quantity']);

            db()->entityManager()->persist($order);
        }

        db()->entityManager()->flush();
        db()->entityManager()->commit();
    }

    /**
     * Получить список основных заказов
     */
    public function listProdOrders($userId) {

        return db()->entityManager()->getRepository(ProdOrders::class)
            ->createQueryBuilder('o')
            ->select('o.id, o.status, o.price, o.comment')
            ->where('o.user = :user_id')
            ->setParameter('user_id', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Получить список всех заказов
     */
    public function listAllOrders($prodOrder) {

        $listOrders = [];

        foreach ($prodOrder as $item) {
            $orders = db()->entityManager()->getRepository(Orders::class)
                ->findBy(['prod_order_id' => $item['id']]);

            $result = [];
            foreach ($orders as $order) {
                $result[] = [
                    'prod_order_id' => $order->getProdOrderId(),
                    'product_id' => $order->getProduct(),
                    'quantity' => $order->getQuantity()
                ];
            }
            $listOrders[] = $item;
            $listOrders[] = $result;
        }

        return $listOrders;
    }
}