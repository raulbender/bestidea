<?php

declare(strict_types=1);

namespace App\Components\Room;

use Framework\BaseController;
use Framework\Http\Request;
use Framework\Http\ResponseDTO;

class RoomController extends BaseController {
  public function __construct(private RoomServiceInterface $roomService) {
    parent::__construct();
  }


  public function index(): ResponseDTO {
    return $this->render('room/index');
  }


  public function create(Request $request): ResponseDTO {
    $description = $request->pullString('description');

    $uuid = $this->roomService->createRoom($description);
    
    return $this->redirect(route('room_view', ['uuid' => $uuid])); // Redireciona para evitar reenvio de formulário (F5)
  }


public function view(Request $request): ResponseDTO {
    $uuid = $request->getAttribute('uuid');

    $room = $this->roomService->getRoomByUuid($uuid);
    if (!$room) {
      throw new \RuntimeException("Sala não encontrada ou expirada.", 404);
    }

    $cookieName = "auth_room_{$uuid}";
    $cookies = $request->getCookieParams();
    $authorId = $cookies[$cookieName] ?? null;

    // 🔥 O XEQUE-MATE: Se não tem cookie, devolvemos a "Ponte de Confiabilidade"
   if (!$authorId) {
        $roomDTO = $this->roomService->getRoomDTO($uuid, null); //
        return $this->cookieBridge($cookieName, (string)$roomDTO->author_id, route('room_view', ['uuid' => $uuid]));
    }

    $roomDTO = $this->roomService->getRoomDTO($uuid, $authorId);

    return $this->render('room/view', $roomDTO);
  }

}
