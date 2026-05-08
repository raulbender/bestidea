<?php

declare(strict_types=1);

// No Controller da Sala
/*
$roomUuid = $request->getAttribute('uuid');
$identities = $session->get('room_identities', []);

if (!isset($identities[$roomUuid])) {
    // Sorteia um ID de autor animal do banco
    $authorId = $this->db->query("SELECT id FROM authors WHERE type = 0 ORDER BY RAND() LIMIT 1")->fetchColumn();
    
    $identities[$roomUuid] = $authorId;
    $session->set('room_identities', $identities);
}

$currentAuthorId = $identities[$roomUuid];
*/