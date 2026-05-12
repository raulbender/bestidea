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


  public function index(): ResponseDTO {
    return $this->render('room/index');
  }


  public function create(Request $request): ResponseDTO {
    $description = $request->pullString('description');

    $uuid = $this->service->createRoom($description);

    // Redireciona para evitar reenvio de formulário (F5)
    return $this->redirect(route('room_view', ['uuid' => $uuid]));
  }


  public function view(Request $request): ResponseDTO {
    $uuid = $request->getAttribute('uuid');

    $room = $this->service->getRoomByUuid($uuid);
    if (!$room) {
      throw new \RuntimeException("Sala não encontrada ou expirada.", 404);
    }

    $cookieName = "auth_room_{$uuid}";
    $cookies = $request->getCookieParams();
    $authorId = $cookies[$cookieName] ?? null;

    $roomDTO = $this->service->getRoomDTO($uuid, $authorId);

    $response = $this->render('room/view', $roomDTO);

    if (!isset($cookies[$cookieName])) {
      $response->headers['Set-Cookie'] = $this->cookie($cookieName, (string)$roomDTO->author_id);
    }

    return $response;
  }


}
