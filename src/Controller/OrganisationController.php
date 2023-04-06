<?php

namespace App\Controller;

use App\Entity\Organisation;
use App\Repository\BuildingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\OrganisationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class OrganisationController extends AbstractController
{

    /**
     * Cette méthode permet de créer une organisation et de retourner la location/l'URL de l'objet créé.
     * 
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/organisations', name:"createOrganisation", methods: ['POST'])]
    public function createOrganisation(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse 
    {

        $organisation = $serializer->deserialize($request->getContent(), Organisation::class, 'json');
        $em->persist($organisation);
        $em->flush();

        $jsonOrganisation = $serializer->serialize($organisation, 'json');
        
        $location = $urlGenerator->generate('detailOrganisation', ['id' => $organisation->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonOrganisation, Response::HTTP_CREATED, ["Location" => $location], true);
   }

   /**
    * Cette méthode permet de récupérer toutes les orgnisations de la base de données.
    *
    * @param OrganisationRepository $organisationRepository
    * @param SerializerInterface $serializer
    * @return JsonResponse
    */
   #[Route('/api/organisations', name: 'listOrganisation')]
    public function getOrganisationList(OrganisationRepository $organisationRepository, SerializerInterface $serializer): JsonResponse
    {
        $organisationList = $organisationRepository->findAll();
        $jsonOrganisationList = $serializer->serialize($organisationList, 'json');
        return new JsonResponse($jsonOrganisationList, Response::HTTP_OK, [], true);

    }

    /**
     * Cette méthode permet de récupérer une organisation spécifique par son ID.
     * 
     * @param OrganisationRepository $organisationRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/organisations/{id}', name: 'detailOrganisation', methods: ['GET'])]
    public function getOrganisationById(Organisation $organisation, SerializerInterface $serializer): JsonResponse
    {
        $jsonOrganisation = $serializer->serialize($organisation, 'json');
        return new JsonResponse($jsonOrganisation, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    /**
     * Cette méthode permet de mettre à jour une organisation tout en modifiant ses buildings.
     * 
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param Organisation $currentOrganisation
     * @param EntityManagerInterface $em
     * @param BuildingRepository $buildingRepository
     * @return JsonResponse
     */
    #[Route('/api/organisations/{id}', name: 'updateOrganisation', methods: ['PUT'])]
    public function updateOrganisation(Request $request, SerializerInterface $serializer, Organisation $currentOrganisation, EntityManagerInterface $em, BuildingRepository $buildingRepository): JsonResponse 
    {
        $updatedOrganisation = $serializer->deserialize($request->getContent(), 
                Organisation::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentOrganisation]);
        $idBuildings = $request->request->get('idBuildings');
        if (is_array($idBuildings)) {
            $buildings = $buildingRepository->find($idBuildings);
            foreach ($buildings as $building) {
                $updatedOrganisation->addBuilding($building);
            }
        }
        
        $em->persist($updatedOrganisation);
        $em->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
   }

   /**
    * Cette méthode permet de supprimer une organisation.
    * @param Organisation $organisation
    * @param EntityManagerInterface $em
    * @return JsonResponse
    */
   #[Route('/api/organisations/{id}', name: 'deleteOrganisation', methods: ['DELETE'])]
    public function deleteOrganisation(Organisation $organisation, EntityManagerInterface $em): JsonResponse 
    {
        $em->remove($organisation);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

}
