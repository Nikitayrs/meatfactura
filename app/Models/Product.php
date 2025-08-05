<?php

namespace App\Models;

use PHPFramework\Model;

use App\Entity\Products;

class Product extends Model
{
    protected array $loaded = ['name', 'description', 'price', 'category', 'availability'];

    /**
     * Получаем все продукты с поддержкой пагинации
     * @return Products[]
     */
    public function findAll(int $limit = 0, int $offset = 0): array
    {
        $query = db()->entityManager()->createQueryBuilder()
            ->select('p')
            ->from(Products::class, 'p')
            ->orderBy('p.id', 'ASC');

        // Добавляем ограничения пагинации если указаны
        if ($limit > 0) {
            $query->setMaxResults($limit);
        }
        
        if ($offset > 0) {
            $query->setFirstResult($offset);
        }

        $lists = $query->getQuery()->getResult();

        $result = [];

        foreach ($lists as $item) {
            $result[] = [
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'price' => $item->getPrice(),
                'category' => $item->getCategory(),
                'availability' => $item->getAvailability()
            ];
        }

        return $result;
    }


    /**
     * Получаем общее количество продуктов
     * @return int
     */
    public function count(): int
    {            
        $query = db()->entityManager()->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(Products::class, 'p')
            ->getQuery();

        return (int) $query->getSingleScalarResult();
    }
}