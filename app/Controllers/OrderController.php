<?php

namespace App\Controllers;

use App\Models\Order;
// use PHPFramework\Pagination;

class OrderController extends BaseController
{
    private $maxProducts = 10;

    public function create()
    {
        $model = new Order();
        $userId = $model->loadDataOne('user_id');
        $products = $model->loadDataOne('products');
        $comment = $model->loadDataOne('comment');

        $products = json_decode($products, true);

        if (count($products) > $this->maxProducts) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'data' => 'Maximum 10 items per order']);
            die();
        }

        // user id принадлежит текущему пользователю
        if(session()->get('user')['id'] != $userId) {
            response()->setResponseCode(400);
            echo json_encode(['status' => 'error', 'data' => 'Undefined user']);
            die;
        }

        try {
            $total = $model->calcTotalAmout($products);

            if(!$total) {
                response()->setResponseCode(404);
                echo json_encode(['status' => 'error', 'data' => 'Invalid product or quantity']);
                die();
            }

            db()->entityManager()->beginTransaction();

            try {
                $prodOrders = $model->createMainOrder($userId, $total, $comment);

                $model->createItemOrder($products, $prodOrders);

                return json_encode([
                    'status' => 'success',
                    'order_id' => $prodOrders->getId(),
                    'total' => $prodOrders->getPrice()
                ]);
            } catch (\Exception $e) {
                $model->secureRollbackVerification();
                
                response()->setResponseCode(500);
                return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        } catch (Doctrine_Connection_Exception $e) {
            response()->setResponseCode(400);
            return json_encode(['status' => 'error', 'data' => "Message: $e->getPortableMessage()"]);;
        }
    }

    public function getUserOrders() 
    {
        $model = new Order();
        $userId = $model->loadDataOne('user_id');

        $prodOrder = $model->listProdOrders($userId);

        if (!$prodOrder) {
            return json_encode(['status' => 'success', 'data' => 'Список заказов пуст']);
        }

        $listOrders = $model->listAllOrders($prodOrder);

        if ($listOrders) {
            return json_encode(['status' => 'success', 'data' => $listOrders]);
        } else {
            return json_encode(['status' => 'success', 'data' => 'Список заказов пуст']);
        }
    }

}