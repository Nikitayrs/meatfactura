<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\SmsService;
use App\Models\PhoneVerification as PhoneModel;
use PHPFramework\Auth;
use PHPFramework\Pagination;
use PHPFramework\Response;

class UserController extends BaseController
{

    private $expiryMin = 5; // время жизни кода в минутах

    /**
     * Отправка кода верификации
     */
    public function sendVerificationCode()
    {
        $model = new User();
        $model->loadData();

        if (empty($model->attributes['name']) && empty($model->attributes['password']) && empty($model->attributes['phone'])) {
            response()->setResponseCode(400);
            return json_encode(['status' => 'error', 'data' => 'No data provided']);
        }
        
        if(!($phone = $model->validNumber($model->attributes['phone']))) {
            response()->setResponseCode(400);
            return json_encode(['status' => 'error', 'data' => 'phone']);
        }

        // существует ли уже такой пользователь
        try {
            $model->checkUserPhone($phone);
        } catch (\Exception $e) {
            response()->setResponseCode(400);
            return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }

        $smsService = new SmsService();
        $code = $smsService->generateCode();
        $ver = new PhoneModel();
        $isVer = $ver->createOrUpdateVerification($model->attributes['phone'], $code, $this->expiryMin);

        if(!$isVer) {
            response()->setResponseCode(500);
            return json_encode(['status' => 'error', 'data' => 'Server error!']);
        }

        $message = "Ваш код подтверждения: $code";

        // Отправляем SMS
        if ($smsService->sendSms($phone, $message)) {
            response()->setResponseCode(201);
            return json_encode(['status' => 'success', 'data' => 'Код отправлен']);
        }

        response()->setResponseCode(400);
        return json_encode(['status' => 'error', 'data' => 'Ошибка отправки SMS']);
    }

    /**
     * Регистрация по номеру телефона
     */
    public function registerByPhone()
    {
        $model = new User();
        $model->loadData();

        if (empty($model->attributes['name']) && empty($model->attributes['password']) && empty($model->attributes['phone'] && empty($model->attributes['address']) && empty($model->attributes['code']))) {
            response()->setResponseCode(400);
            return json_encode(['status' => 'error', 'data' => 'No data provided']);
        }
            
        $isAjax = request()->isAjax();

        if(!($phone = $model->validNumber($model->attributes['phone']))) {
            response()->setResponseCode(400);
            return json_encode(['status' => 'error', 'data' => 'phone']);
        }

        // существует ли уже такой пользователь
        try {
            $model->checkUserPhone($phone);
        } catch (\Exception $e) {
            response()->setResponseCode(400);
            return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }

        // Проверка верификационного кода
        $verification = new PhoneModel();
        if (!$verification->verify($model->attributes['phone'], $model->attributes['code'])) {
            return json_encode(['status' => 'error', 'data' => 'Неверный код или истек срок действия']);
        }

        try {
            $user = $model->createUser(
                $model->attributes['name'], 
                $model->attributes['password'], 
                $model->attributes['phone'],
                $model->attributes['address']
            );
            
            if (!$model->validate($user)) {
                if ($isAjax) {
                    response()->setResponseCode(400);
                    echo json_encode(['status' => 'error', 'data' => $model->getErrors()]);
                    die;
                } else {
                    session()->setFlash('error', implode('<br>', $model->getErrors()));
                    session()->set('form_errors', $model->getErrors());
                    session()->set('form_data', $model->attributes);
                    response()->redirect('/register');
                    die;
                }   
            }

            if ($user) {
                if ($isAjax) {
                    response()->setResponseCode(200);
                    echo json_encode([
                        'status' => 'success',
                        'data' => sprintf(__('user_success'), $user->getId()),
                        'redirect' => base_href('/login')
                    ]);
                    die;
                } else {
                    session()->setFlash('success', sprintf(__('user_success'), $user->getId()));
                    response()->redirect('/login');
                    die;
                }
            }
        } catch (\Exception $e) {
            $errorMessage = 'Ошибка регистрации: ' . $e->getMessage();
            
            if ($isAjax) {
                response()->setResponseCode(500);
                echo json_encode(['status' => 'error', 'data' => $errorMessage]);
                die;
            } else {
                session()->setFlash('error', $errorMessage);
                session()->set('form_data', $model->attributes);
                response()->redirect('/register');
                die;
            }
        }

        if (!$isAjax) {
            response()->redirect('/register');
        }
    }

    /**
     * Авторизация пользователя
     */
    public function auth()
    {
        $model = new User();
        $model->loadData();

        if (empty($model->attributes['name']) && empty($model->attributes['password'])) {
            response()->setResponseCode(400);
            echo json_encode(['status' => 'error', 'data' => 'No data provided']);
            die;
        }

        $existingUser = $model->searchUser($model->attributes['name']);

        if (!$existingUser) {
            response()->setResponseCode(401);
            echo json_encode(['status' => 'error', 'data' => 'User not found']);
            die;
        }

        try {
            if (Auth::login([
                'name' => $model->attributes['name'],
                'password' => $model->attributes['password'],
            ])) {
                response()->setResponseCode(200);
                echo json_encode([
                    'status' => 'success', 
                    'data' => 'Success login', 
                    'redirect' => base_href('/')]
                );
            } else {
                response()->setResponseCode(401);
                echo json_encode(['status' => 'error', 'data' => 'Authentication failed']);
            }
        } catch (\Exception $e) {
            response()->setResponseCode(500);
            echo json_encode(['status' => 'error', 'data' => 'Server error: ' . $e->getMessage()]);
        }
        die;
    }

    /**
     * Выход пользователя из системы
     */
    public function logout()
    {
        Auth::logout();
        response()->redirect(base_href('/login'));
    }

}