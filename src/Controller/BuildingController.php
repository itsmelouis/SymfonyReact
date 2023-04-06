<?php

namespace App\Controller;

use App\Entity\Building;
use App\Repository\BuildingRepository;
use App\Repository\OrganisationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class BuildingController extends AbstractController
{
    /**
     * Cette méthode permet de créer un building et de retourner la location/l'URL de l'objet créé.
     * 
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/buildings', name: 'createBuilding', methods: ['POST'])]
    public function createBuilding(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse 
    {
        $building = $serializer->deserialize($request->getContent(), Building::class, 'json');
        $em->persist($building);
        $em->flush();

        $jsonBuilding = $serializer->serialize($building, 'json');
        $location = $urlGenerator->generate('detailBuilding', ['id' => $building->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        
        return new JsonResponse($jsonBuilding, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    /**
    * Cette méthode permet de récupérer tous les buildings de la base de données.
    *
    * @param OrganisationRepository $organisationRepository
    * @param SerializerInterface $serializer
    * @return JsonResponse
    */
    #[Route('/api/buildings', name: 'listBuildings', methods: ['GET'])]
    public function getOrganisationList(BuildingRepository $buildingRepository, SerializerInterface $serializer): JsonResponse
    {
        $buildingList = $buildingRepository->findAll();
        $jsonOrganisationList = $serializer->serialize($buildingList, 'json');
        return new JsonResponse($jsonOrganisationList, Response::HTTP_OK, [], true);

    }

    /**
     * Cette méthode permet de récupérer un building spécifique par son ID.
     * 
     * @param OrganisationRepository $organisationRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/buildings/{id}', name: 'detailBuilding', methods: ['GET'])]
    public function getOrganisationById(Building $building, SerializerInterface $serializer): JsonResponse
    {
        $jsonBuilding = $serializer->serialize($building, 'json');
        return new JsonResponse($jsonBuilding, Response::HTTP_OK, ['accept' => 'json'], true);

    }
    
    /**
     * Cette méthode permet de mettre à jour un building tout en modifiant ses organisations.
     * 
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param Building $currentBuilding
     * @param EntityManagerInterface $em
     * @param OrganisationRepository $organisationRepository
     * @return JsonResponse
     */
    #[Route('/api/buildings/{id}', name: 'updateBuilding', methods: ['PUT'])]
    public function updateBuilding(Request $request, SerializerInterface $serializer, Building $currentBuilding, EntityManagerInterface $em, OrganisationRepository $organisationRepository): JsonResponse 
    {
        $updatedBuilding = $serializer->deserialize($request->getContent(), 
                Building::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentBuilding]);
        $idOrganisations = $request->request->get('idOrganisations');
        if (is_array($idOrganisations)) {
            $organisations = $organisationRepository->find($idOrganisations);
            foreach ($organisations as $organisation) {
                $updatedBuilding->addOrganisation($organisation);
            }
        }
        $em->persist($updatedBuilding);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    
    /**
    * Cette méthode permet de supprimer un building.
    * @param Building $building
    * @param EntityManagerInterface $em
    * @return JsonResponse
    */
    #[Route('/api/buildings/{id}', name: 'deleteBuilding', methods: ['DELETE'])]
    public function deleteBuilding(Building $building, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($building);
        $em->flush();
        
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Cette méthode permet de récupérer le nombre de personnes au total dans un building.
     * 
     * @param Building $building
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/api/buildings/{id}/occupancy', name: 'getBuildingOccupancy', methods: ['GET'])]
    public function getBuildingOccupancy(Building $building, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $rooms = $em->getRepository(Room::class)->findBy(['building' => $building]);
        $occupancy = 0;
        foreach ($rooms as $room) {
            $occupancy += $room->getNumberOfPeople();
        }
        $occupancyJson = $serializer->serialize(['occupancy' => $occupancy], 'json');
        return new JsonResponse($occupancyJson, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}
