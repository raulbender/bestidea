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




  // public function view(Request $request): ResponseDTO {
  //   $uuid = $request->getAttribute('uuid');

  //   if (!$uuid) {
  //     return $this->redirect(route('home'));
  //   }

  //   $room = $this->service->getRoomByUuid($uuid);
  //   if (!$room) {
  //     throw new \RuntimeException("Sala não encontrada ou expirada.", 404);
  //   }
  //   $roomDTO = $this->makeRoomDTO($room);

  //   return $this->render('room/view', $roomDTO);
  // }

  public function view(Request $request): ResponseDTO {
    $uuid = $request->getAttribute('uuid');

    if (!$uuid) {
      return $this->redirect(route('home'));
    }

    $room = $this->service->getRoomByUuid($uuid);
    if (!$room) {
      throw new \RuntimeException("Sala não encontrada ou expirada.");
    }

    // --- Lógica do Cookie (O Coração da Autenticação por Sala) ---
    $cookieName = "auth_room_{$uuid}";

    // Verificamos se o navegador já enviou esse cookie
    // Como você usa PSR-7, pegamos via getCookieParams da Request
    $cookies = $request->getCookieParams(); // Se seu framework não tiver esse atalho, usamos $_COOKIE
    $authorId = $cookies[$cookieName] ?? null;

    if (!$authorId) {
      // Simulação: Sorteamos o autor ID 10
      $authorId = 10;

      // Criamos a resposta e anexamos o cookie
      $roomDTO = $this->makeRoomDTO($room);
      $response = $this->render('room/view', $roomDTO);

      // Definimos o cookie para durar enquanto a sala existir (ou 24h)
      // Set-Cookie: auth_room_uuid=10; Path=/; HttpOnly
      $response->headers['Set-Cookie'] = $this->cookie("auth_room_{$uuid}", "10");

      return $response;
    }

    // Se já tem o cookie, apenas renderiza normalmente
    $roomDTO = $this->makeRoomDTO($room);
    return $this->render('room/view', $roomDTO);
  }


  private function makeRoomDTO(RoomEntity $room): RoomDTO {
    return new RoomDTO(
      uuid: $room->uuid,
      description: $room->description,
      expires_at: $room->expires_at . "Z"
    );
  }
}
