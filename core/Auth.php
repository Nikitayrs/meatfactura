<?php

namespace PHPFramework;

use App\Entity\Users;

class Auth
{

    public static function login(array $credentials): bool
    {
        $password = $credentials['password'];
        unset($credentials['password']);
        $field = array_key_first($credentials);
        $value = $credentials[$field];
        $query = db()->queryBuilder()
            ->select('u.id, u.name, u.password')
            ->from(Users::class, 'u')
            ->where('u.name = :name')
            ->setParameter('name', $value)
            ->getQuery();

        $user = $query->getResult()[0];

        if (!$user) {
            response()->setResponseCode(404);
            return false;
        }

        if (password_verify($password, $user['password'])) {
            session()->set('user', [
                'id' => $user['id'],
                'name' => $user['name'],
            ]);
            return true;
        }
        return false;
    }

    public static function user()
    {
        return session()->get('user');
    }

    public static function isAuth(): bool
    {
        return session()->has('user');
    }

    public static function logout(): void
    {
        session()->remove('user');
        response()->redirect(base_href('/login'));
    }

    public static function setUser(): void
    {
        if ($user_data = self::user()) {
            $query = db()->queryBuilder()
                ->select('u.id, u.name, u.password')
                ->from(Users::class, 'u')
                ->where('u.id = :id')
                ->setParameter('id', $user_data['id'])
                ->getQuery();

            $data = $query->getResult()[0];

            if (!$data) {
                response()->setResponseCode(404);
            }

            if ($data) {
                session()->set('user', [
                    'id' => $data['id'],
                    'name' => $data['name'],
                ]);
            }
        }
    }

}