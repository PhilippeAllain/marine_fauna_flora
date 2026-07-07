<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\FishRepository;
use App\Model\SearchData;
use App\Form\SearchType;


#[Route('/fish', name: 'fish.', methods: ['GET'])]

final class FishController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, FishRepository $fishRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $fishes = $fishRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('fish/index.html.twig', [
                'form' => $form,
                'fishes' => $fishes
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $fishes = $fishRepository->paginateFishes($page);
        return $this->render('fish/index.html.twig', [
            'form' => $form,
            'fishes' => $fishes,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, FishRepository $fishRepository): Response
    {
        $fish = $fishRepository->find($id);

        if (!$fish) {
            throw $this->createNotFoundException('Fish not found');
        }

        return $this->render('fish/show.html.twig', [
            'fish' => $fish
        ]);
    }

}
