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
      return $this->render('room/create');
    }

    public function create(Request $request): ResponseDTO
    {
        $description = $request->input('description');

        // Aqui você pode adicionar a lógica para criar a sala, por exemplo, salvando no banco de dados
        // e gerando um ID ou UUID para a sala.

        // Para este exemplo, vamos apenas redirecionar para a página da sala criada.
        // Suponha que o ID da sala seja 123 (você deve substituir isso pela lógica real de criação).
        $roomId = 123; // Substitua pela lógica real de criação de sala

        return $this->redirect(route('room_view', ['id' => $roomId]));
    }

}

