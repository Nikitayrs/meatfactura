<?php

namespace App\Controllers\Admin;

use App\Models\Review;
use App\Controllers\BaseController;

class AdminReviewController extends BaseController
{
    public function index()
    {
        $idCompany = (int)get_route_param('id');
        $idReview = (int)get_route_param('id_review');

        $review = db()->query("SELECT * FROM reviews WHERE company_id=? AND id=?", [$idCompany, $idReview])->get();

        if (!$review) {
            return view('/admin/company/' . $idCompany . '/review/' . $idReview, [
                'error' => 'Такой компании не существует',
            ]);
        }

        if($review[0]['photo']) {
            $createdAt = new \DateTime($review[0]['updated_at']);
            $filePath = UPLOADS . sprintf(
                'reviews/%s/%s/%s/%s',
                $createdAt->format('Y'),
                $createdAt->format('m'),
                $createdAt->format('d'),
                $review[0]['photo']
            );

            return view('review/index', [
                'id' => $review[0]["id"],
                'id_company' => $idCompany,
                'author_name' => $review[0]["author_name"],
                'photo' => $filePath,
                'review_text' => $review[0]["review_text"]
            ]);
        } else {
            return view('review/index', [
                'id' => $review[0]["id"],
                'id_company' => $idCompany,
                'author_name' => $review[0]["author_name"],
                'review_text' => $review[0]["review_text"]
            ]);
        }  
    }

    public function approveReview()
    {
        $idCompany = (int)get_route_param('id');
        $idReview = (int)get_route_param('review');

        $review = db()->query("SELECT * FROM reviews WHERE company_id=? AND id=?", [$idCompany, $idReview])->get();

        if (!$review) {
            return view('/admin/company/' . $idCompany . '/review/' . $idReview, [
                'error' => 'Такой компании не существует',
            ]);
        }

        db()->query("UPDATE reviews SET status = 'approved' WHERE company_id=? AND id=?", [$idCompany, $idReview])->get();

        return json_encode(['status' => 'success', 'data' => 'Отзыв добрен.']);
    }

    public function deniedReview()
    {
        $idCompany = (int)get_route_param('id');
        $idReview = (int)get_route_param('id_review');

        $review = db()->query("SELECT * FROM reviews WHERE company_id=? AND id=?", [$idCompany, $idReview])->get();

        if (!$review) {
            return view('/admin/company/' . $idCompany . '/review/' . $idReview, [
                'error' => 'Такой компании не существует',
            ]);
        }

        db()->query("UPDATE reviews SET status = 'denied' WHERE company_id=? AND id=?", [$idCompany, $idReview])->get();

        return json_encode(['status' => 'success', 'data' => 'Отзыв заблокирован.']);
    }
}