<?php

declare(strict_types=1);

namespace App\Components\Room;

use Framework\BaseController;
use Framework\Http\Request;
use Framework\Http\ResponseDTO;

class RoomController extends BaseController
{
    public function index(Request $request): ResponseDTO
    {
      return $this->render('room/index');
    }

}
