<?php

namespace App\Controllers\Admin;

use App\Models\User;
use App\Models\Company;
use App\Models\Review;
use PHPFramework\File;
use PHPFramework\Pagination;
use App\Controllers\BaseController;

class AdminCompanyController extends BaseController
{
    public function index()
    {
        // URL адресс
        $id = (int)get_route_param('id');

        // ищем компанию в БД
        $company = db()->query("SELECT id, name, logo, description FROM companies WHERE id=?", [$id])->get();

        // Получаем общее количество компаний
        $reviews_cnt = db()->query("SELECT COUNT(*) FROM reviews")->getColumn();

        // Создаём объект пагинации
        $pagination = new Pagination($reviews_cnt, REVIEWS_PER_PAGE, tpl: 'pagination/base2', midSize: 3);

        // Получаем список отзывов об этой компании с учётом пагинации
        $reviews = db()->query("SELECT * FROM reviews WHERE company_id=? LIMIT " . REVIEWS_PER_PAGE . " OFFSET " . $pagination->getOffset(), [$id])->get();

        // Если компания не найдена - 404
        if (!$company) {
            return view('company/index', [
                'id' => 'no',
                'title' => 'Такой компании не существует',
                'description' => '',
                'logo' => ''
            ]); 
        }

        return view('company/index', [
            'id' => $company[0]["id"],
            'title' => $company[0]["name"],
            'description' => $company[0]["description"],
            'logo' => $company[0]["logo"],
            'reviews' => $reviews,
            'pagination' => $pagination,
        ]);
    }

    public function review() {
        $model = new Review();
        $file = new File('photo');

        $file_name = $file->getSaveName();

        // Загружаем данные из запроса (например, из формы)
        $model->loadData('photo', $file_name);

        // Устанавливаем company_id
        $model->attributes['company_id'] = get_route_param('id'); // Пример ID компании

        // Валидируем данные
        if ($model->validate()) {
            // Если валидация прошла успешно, сохраняем в базу
            $reviewId = $model->save();

            $file_url = $file->save('reviews');

            if ($reviewId) {
                echo json_encode(['status' => 'success', 'data' => 'Отзыв ID: ' . $reviewId . ' отправлен на модерацию']);
            } else {
                echo "Ошибка при сохранении отзыва.";
            }
        } else {
            // Если есть ошибки, выводим их
            echo json_encode(['status' => 'error', 'data' => $model->listErrors()]);
            die;
        }
    }
}