<?php

declare(strict_types=1);

namespace App\Components\Home;

use Framework\BaseController;
use Framework\Http\Request;
use Framework\Http\ResponseDTO;

class HomeController extends BaseController
{
    public function index(Request $request): ResponseDTO
    {
      return $this->render('home/index');
    }
}
