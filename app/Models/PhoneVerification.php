<?php

namespace App\Models;

use PHPFramework\Model;

use App\Entity\PhoneVerification as PhoneEntity;

class PhoneVerification extends Model
{
    protected array $loaded = ['phone', 'code', 'expiresAt'];

    /**
     * Создает или обновляет запись верификации
     * @param string $phone
     * @param string $code
     * @param int $expiryMin - время жизни кода в минутах
     * @return bool
     */
    public function createOrUpdateVerification(string $phone, string $code, int $expiryMin = 5): bool
    {           
        $existing = db()->entityManager()->getRepository(PhoneEntity::class)->findOneBy(['phone' => $phone]);

        try {
            if (!$existing) {
                $verification = new PhoneEntity();
                $verification->setPhone($phone);
            } else {
                $verification = $existing;
            }

            $expiresAt = new \DateTime("+$expiryMin minutes");
            
            $verification
                ->setCode($code)
                ->setExpiresAt($expiresAt);
                
            db()->entityManager()->persist($verification);
            db()->entityManager()->flush();
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Проверяет код верификации
     * @param string $phone
     * @param string $code
     * @return bool
     */
    public function verify(string $phone, string $code): bool
    {
        $currentTime = new \DateTime();
    
        $verification = db()->entityManager()->getRepository(PhoneEntity::class)
            ->createQueryBuilder('p')
            ->where('p.phone = :phone')
            ->andWhere('p.code = :code')
            ->andWhere('p.expiresAt > :currentTime')
            ->setParameter('phone', $phone)
            ->setParameter('code', $code)
            ->setParameter('currentTime', $currentTime)
            ->getQuery()
            ->getOneOrNullResult();

        return $verification !== null;
    }
}