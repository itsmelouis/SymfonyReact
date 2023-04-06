<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class RoomController extends AbstractController
{
    /**
     * Cette méthode permet de créer une pièce et de retourner la location/l'URL de l'objet créé.
     * 
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/rooms', name: 'createRoom', methods: ['POST'])]
    public function createRoom(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse 
    {
        $room = $serializer->deserialize($request->getContent(), Room::class, 'json');
        $em->persist($room);
        $em->flush();

        $jsonRoom = $serializer->serialize($room, 'json');
        
        $location = $urlGenerator->generate('detailRoom', ['id' => $room->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonRoom, Response::HTTP_CREATED, ["Location" => $location], true);        
    }

    /**
    * Cette méthode permet de récupérer toutes les pièces de la base de données.
    *
    * @param OrganisationRepository $organisationRepository
    * @param SerializerInterface $serializer
    * @return JsonResponse
    */
    #[Route('/api/rooms', name: 'listRooms', methods: ['GET'])]
    public function getRoomList(RoomRepository $roomRepository, SerializerInterface $serializer): JsonResponse
    {
        $roomList = $roomRepository->findAll();

        $jsonRoomList = $serializer->serialize($roomList, 'json');
        return new JsonResponse($jsonRoomList, Response::HTTP_OK, [], true);

    }

    /**
    * Cette méthode permet de récupérer une pièce spécifique par son ID.
    *
    * @param OrganisationRepository $organisationRepository
    * @param SerializerInterface $serializer
    * @return JsonResponse
    */
    #[Route('/api/rooms/{id}', name: 'detailRoom', methods: ['GET'])]
    public function getRoomById(Room $room, SerializerInterface $serializer): JsonResponse
    {
        $jsonRoom = $serializer->serialize($room, 'json');
        return new JsonResponse($jsonRoom, Response::HTTP_OK, [], true);
    }
    
    /**
     * Cette méthode permet de mettre à jour une pièce.
     * 
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param Room $currentRoom
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/api/rooms/{id}', name: 'updateRoom', methods: ['PUT'])]
    public function updateRoom(Request $request, SerializerInterface $serializer, Room $currentRoom, EntityManagerInterface $em): JsonResponse 
    {
        $updatedRoom = $serializer->deserialize($request->getContent(), 
                Room::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentRoom]);
        $em->persist($updatedRoom);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    
    /**
    * Cette méthode permet de supprimer une pièce.
    * @param Room $room
    * @param EntityManagerInterface $em
    * @return JsonResponse
    */
    #[Route('/api/rooms/{id}', name: 'deleteRoom', methods: ['DELETE'])]
    public function deleteRoom(Room $room, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($room);
        $em->flush();
        
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Cette méthode permet de récupérer le nombre de personnes dans une pièce.
     * @param Room $room
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/rooms/{id}/occupancy', name: 'getRoomOccupancy', methods: ['GET'])]
    public function getRoomOccupancy(Room $room, SerializerInterface $serializer): JsonResponse
    {
        $occupancy = $serializer->serialize($room->getNumberOfPeople(), 'json');
        return new JsonResponse($occupancy, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}
