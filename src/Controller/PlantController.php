<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\PlantRepository;
use App\Model\SearchData;
use App\Form\SearchType;


#[Route('/plant', name: 'plant.', methods: ['GET', 'POST'])]

final class PlantController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, PlantRepository $plantRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $plants = $plantRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('plant/index.html.twig', [
                'form' => $form,
                'plants' => $plants
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $plants = $plantRepository->paginatePlants($page);
        return $this->render('plant/index.html.twig', [
            'form' => $form,
            'plants' => $plants,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, PlantRepository $plantRepository): Response
    {
        $plant = $plantRepository->find($id);

        if (!$plant) {
            throw $this->createNotFoundException('Plant not found');
        }

        return $this->render('plant/show.html.twig', [
            'plant' => $plant
        ]);
    }

}
