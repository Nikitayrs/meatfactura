<?php

namespace App\Controllers\Admin;

use App\Models\Admin;
use App\Models\Company;
use PHPFramework\Auth;
use PHPFramework\File;
use PHPFramework\Pagination;
use App\Controllers\BaseController;

class AdminController extends BaseController
{

    public function login()
    {
        return view('admin/login', [
            'title' => 'Login page',
        ]);
    }

    public function auth()
    {
        $model = new Admin();

        $model->loadData();

        if (!$model->validate($model->attributes, [
            'required' => ['login', 'password'],
        ])) {
            var_dump($model->listErrors());
            echo json_encode(['status' => 'error', 'data' => $model->listErrors()]);
            die;
        }

        if (Auth::login([
            'login' => $model->attributes['login'],
            'password' => $model->attributes['password'],
        ])) {
            echo json_encode(['status' => 'success', 'data' => 'Вы успешно авторизовались', 'redirect' => base_href('/')]);
        } else {
            var_dump($model->listErrors());
            echo json_encode(['status' => 'error', 'data' => 'Неверный логин или пароль']);
        }
        die;
    }

    public function updateReview() {
        // Массив допустимых значений
        $allowedStatuses = ['pending', 'approved', 'denied'];
        
        // Получаем ID компании
        $reviewId = (int)get_route_param('review_id');
        // Имя автора
        $author_name = trim(get_route_param('author_name'));
        // Текст отзыва
        $review_text = trim(get_route_param('review_text'));
        $removePhoto = get_route_param('remove_photo') == 1;
        if (in_array(get_route_param('review_status'), $allowedStatuses)) {
            $review_status = get_route_param('review_status');
        } else {
            return json_encode(['success' => false, 'error' => 'Не указан статус отзыва'], 400);
        }

        // Валидация
        if (empty($author_name) || empty($review_text)) {
            return json_encode(['success' => false, 'error' => 'Все поля обязательны для заполнения'], 400);
        }

        try {
            // Получаем текущие данные компании
            $review = db()->query("SELECT * FROM reviews WHERE id = ?", [$reviewId])->get();
            if (!$review) {
                return json_encode(['success' => false, 'error' => 'Отзыв не найден'], 404);
            }
    
            $currentImage = $review[0]['image'] ?? '';
            $newImage = $currentImage;
    
            // Обработка фото
            if ($removePhoto) {
                // Удаляем текущее фото
                if (!empty($currentImage)) {
                    // Парсим дату создания для формирования пути
                    $createdAt = new \DateTime($review[0]['updated_at']);
                    $filePath = UPLOADS . sprintf(
                        'reviews/%s/%s/%s/%s',
                        $createdAt->format('Y'),
                        $createdAt->format('m'),
                        $createdAt->format('d'),
                        $review[0]['image']
                    );

                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    $newLogo = '';
                }
            }
    
            $file = new File('logo');
            // Обработка загрузки нового логотипа
            if ($file) {
                $file_name = $file->getSaveName();
                $file_url = $file->save('reviews');

                // Парсим дату создания для формирования пути
                $createdAt = new \DateTime($company[0]['updated_at']);
                $filePath = sprintf(
                    'reviews/%s/%s/%s/%s',
                    $createdAt->format('Y'),
                    $createdAt->format('m'),
                    $createdAt->format('d'),
                    $company[0]['logo']
                );
    
                // Загружаем новое фото
                $file_url = $file->save('companies');
            }

            // Обновляем запись в базе
            $reviewId = $model->save();
    
            // Обновляем запись в базе
            db()->query(
                "UPDATE reviews 
                SET author_name = ?, photo = ?, review_text = ?, status = ? updated_at = NOW() 
                WHERE company_id = ?",
                [$author_name, $file_name, $review_text, 'approved', $companyId]
            );
            if ($reviewId) {
                return json_encode(['status' => 'success', 'data' => 'Данные успешно обновлены.']);
            } else {
                return json_encode(['status' => 'error', 'data' => 'Ошибка при сохранении данных о компании.']);
            }
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteCompany() {
        // Получаем ID компании
        $companyId = (int)get_route_param('id');

        // Удаляем компанию в БД
        db()->query("DELETE FROM companies WHERE id = ?;", [$companyId])->get();

        return json_encode(['success' => true]);
    }

    public function deleteLogoAction() {
        // Получаем ID компании
        $companyId = (int)get_route_param('id');

        // Ищем компанию
        $company = db()->query("SELECT logo FROM companies WHERE id = ?", [$companyId])->get();
        if (!$company) {
            return json_encode(['success' => false, 'error' => 'Company not found'], 404);
        }

        try {
            // Удаляем физический файл логотипа
            if (!empty($company[0]['logo'])) {
                $logoPath = UPLOADS . '/companies/' . $company[0]['logo'];
                if (file_exists($logoPath)) {
                    unlink($logoPath);
                }
            }

            // Обновляем запись в базе данных
            db()->query("UPDATE companies SET logo = '' WHERE id = ?", [$companyId]);

            return json_encode(['success' => true]);

        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Error updating logo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateCompany() {
        // Создаем экземпляр модели
        $model = new Company();

        // Получаем ID компании
        $companyId = (int)get_route_param('company_id');
        $name = trim(get_route_param('name'));
        $description = trim(get_route_param('description'));
        $removeLogo = get_route_param('remove_logo') == 1;
    
        // Валидация
        if (empty($name) || empty($description)) {
            return json_encode(['success' => false, 'error' => 'All fields are required'], 400);
        }
    
        try {
            // Получаем текущие данные компании
            $company = db()->query("SELECT logo, created_at FROM companies WHERE id = ?", [$companyId])->get();
            if (!$company) {
                return json_encode(['success' => false, 'error' => 'Company not found'], 404);
            }
    
            $currentLogo = $company[0]['logo'] ?? '';
            $newLogo = $currentLogo;
    
            // Обработка логотипа
            if ($removeLogo) {
                // Удаляем текущий логотип
                if (!empty($currentLogo)) {
                    // Парсим дату создания для формирования пути
                    $createdAt = new \DateTime($company[0]['updated_at']);
                    $filePath = UPLOADS . sprintf(
                        'reviews/%s/%s/%s/%s',
                        $createdAt->format('Y'),
                        $createdAt->format('m'),
                        $createdAt->format('d'),
                        $company[0]['logo']
                    );

                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    $newLogo = '';
                }
            }
    
            $file = new File('logo');
            // Обработка загрузки нового логотипа
            if ($file) {
                $file_name = $file->getSaveName();
                $file_url = $file->save('reviews');

                // Парсим дату создания для формирования пути
                $createdAt = new \DateTime($company[0]['updated_at']);
                $filePath = sprintf(
                    'reviews/%s/%s/%s/%s',
                    $createdAt->format('Y'),
                    $createdAt->format('m'),
                    $createdAt->format('d'),
                    $company['logo']
                );
    
                // Загружаем новый
                $file_url = $file->save('companies');
            }

            // Обновляем запись в базе
            $reviewId = $model->save();
    
            // Обновляем запись в базе
            db()->query(
                "UPDATE companies 
                SET name = ?, description = ?, logo = ?, updated_at = NOW() 
                WHERE id = ?",
                [$name, $description, $newLogo, $companyId]
            );
            if ($reviewId) {
                return json_encode(['status' => 'success', 'data' => 'Данные успешно обновлены.']);
            } else {
                return json_encode(['status' => 'error', 'data' => 'Ошибка при сохранении данных о компании.']);
            }
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        Auth::logout();
        response()->redirect(base_href('/login'));
    }

    public function index()
    {
        /*if ($page = cache()->get(request()->rawUri)) {
            return $page;
        }*/

        $users_cnt = db()->query("select count(*) from users")->getColumn();
        $limit = PAGINATION_SETTINGS['perPage'];
        $pagination = new Pagination($users_cnt, $limit, tpl: 'pagination/base2', midSize: 3);

        $users = db()->query("select * from users limit $limit offset {$pagination->getOffset()}")->get();

        /*$page = view('user/index', [
            'title' => 'Users',
            'users' => $users,
            'pagination' => $pagination,
        ]);

        cache()->set(request()->rawUri, $page);
        return $page;*/

        return view('admin/index', [
            'title' => 'Users',
            'users' => $users,
            'pagination' => $pagination,
        ]);
    }

}