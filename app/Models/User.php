<?php

namespace App\Models;

use PHPFramework\Model;

use App\Entity\Users;
use App\Entity\PhoneEntity;

class User extends Model
{

    protected array $loaded = ['name', 'password', 'phone', 'address', 'code'];

    /**
     * Создание нового пользователя
     */
    public function createUser(
        string $name,
        string $password,
        string $phone,
        ?string $address = null
    ) {
        // Хеширование пароля
        $hashedPassword = password_hash(
            $password, 
            PASSWORD_DEFAULT
        );
    
        // Создаем и валидируем пользователя
        $user = new Users();
        $user->setName($name)
            ->setPhone($phone)
            ->setAddress($address)
            ->setPassword($hashedPassword);

        if(!$this->validate($user)) {
            throw new \InvalidArgumentException($this->listErrors());
        }
        
        db()->entityManager()->persist($user);
        db()->entityManager()->flush();
        
        return $user;
    }

    /**
     * Проверка существования по номеру
     */
    public function checkUserPhone(string $phone) {
        $result = db()->entityManager()->getRepository(Users::class)
            ->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.phone = :phone')
            ->setParameter('phone', $phone)
            ->getQuery()
            ->getSingleScalarResult();

        if ($result > 0) {
            throw new \RuntimeException('Пользователь с таким телефоном уже существует');
        }
    }

    /**
     * Валидация телефона пользователя
     */
    public function validNumber(string $phone) {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    /**
     * Поиск существующего пользователя
     */
    public function searchUser(string $name) {
        try {
            $user = db()->entityManager()->getRepository(Users::class)
                ->findOneBy(['name' => $name]);
            
            if (!$user) {
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}