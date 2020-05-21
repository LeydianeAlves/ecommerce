<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;

use App\Contracts\CategoryContract;
use App\Http\Controllers\BaseController;

class CategoryController extends BaseController
{
    protected $categoryRepository;

    public function __construct(CategoryContract $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function show($slug)
    {
        $category = $this->categoryRepository->findBySlug($slug);

        if (!$category) {
            // dd($category . $slug);
            return $this->responseRedirectBack('Error occurred while trying to find category', 'error', true, true);
        }

        return view('site.pages.category', compact('category'));
    }
}
