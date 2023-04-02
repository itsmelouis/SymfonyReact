<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\OrganisationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


class OrganisationController extends AbstractController
{
    #[Route('/api/organisations', name: 'app_organisation')]
    public function getOrganisationList(OrganisationRepository $organisationRepository, SerializerInterface $serializer): JsonResponse
    {
        $organisationList = $organisationRepository->findAll();
        $jsonOrganisationList = $serializer->serialize($organisationList, 'json');
        return new JsonResponse($jsonOrganisationList, Response::HTTP_OK, [], true);

    }

    #[Route('/api/organisationss/{id}', name: 'organisation_by_id', methods: ['GET'])]
    public function getOrganisationById(int $id, OrganisationRepository $organisationRepository, SerializerInterface $serializer): JsonResponse
    {
        $organisation = $organisationRepository->find($id);
        if ($organisation) {
            $jsonOrganisation = $serializer->serialize($organisation, 'json');
            return new JsonResponse($jsonOrganisation, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }
}
