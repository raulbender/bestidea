<?php

declare(strict_types=1);

namespace App\Components\Room;

use Framework\BaseController;
use Framework\Http\Request;
use Framework\Http\ResponseDTO;

class RoomController extends BaseController {
  public function __construct(private RoomServiceInterface $service) {
    parent::__construct();
  }


  public function index(Request $request): ResponseDTO {
    return $this->render('room/index');
  }


  public function create(Request $request): ResponseDTO {
    $description = $request->pullString('description');

      $uuid = $this->service->createRoom($description);

      // Redireciona para evitar reenvio de formulário (F5)
      return $this->redirect(route('room_view', ['uuid' => $uuid]));

  }



 
  // GET /{lang}/room/{uuid}
public function view(Request $request): ResponseDTO 
{
    // 1. Captura o UUID que a BaseRoute injetou na Request
    $uuid = $request->getAttribute('uuid');

    if (!$uuid) {
        return $this->redirect(route('home'));
    }

    // 2. Busca os dados da sala através do Service
    $room = $this->service->getRoomByUuid($uuid);

    // 3. Se a sala não existir ou estiver expirada, lidamos com o erro
    if (!$room) {
        throw new \RuntimeException("Sala não encontrada ou expirada.");
    }

    $roomDTO = new RoomDTO(
        uuid: $room->uuid,
        description: $room->description,
        expires_at: $room->expires_at . "Z"
    );
    // 4. Renderiza a view com os dados da sala (description, etc)
    return $this->render('room/view', $roomDTO);
}

}
