<?php

namespace App\Controller;

use App\Repository\RoomRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RoomController extends AbstractController
{
    #[Route('/api/rooms', name: 'rooms', methods: ['GET'])]
    public function getRoomList(RoomRepository $roomRepository, SerializerInterface $serializer): JsonResponse
    {
        $roomList = $roomRepository->findAll();
        $jsonRoomList = $serializer->serialize($roomList, 'json');
        return new JsonResponse($jsonRoomList, Response::HTTP_OK, [], true);

    }

    #[Route('/api/rooms/{id}', name: 'room_by_id', methods: ['GET'])]
    public function getRoomById(int $id, RoomRepository $roomRepository, SerializerInterface $serializer): JsonResponse
    {
        $room = $roomRepository->find($id);
        if ($room) {
            $jsonRoom = $serializer->serialize($room, 'json');
            return new JsonResponse($jsonRoom, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }
}
