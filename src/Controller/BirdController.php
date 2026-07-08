<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\BirdRepository;
use App\Model\SearchData;
use App\Form\SearchType;


#[Route('/bird', name: 'bird.', methods: ['GET', 'POST'])]

final class BirdController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, BirdRepository $birdRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $birds = $birdRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('bird/index.html.twig', [
                'form' => $form,
                'birds' => $birds
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $birds = $birdRepository->paginateBirds($page);
        return $this->render('bird/index.html.twig', [
            'form' => $form,
            'birds' => $birds,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, BirdRepository $birdRepository): Response
    {
        $bird = $birdRepository->find($id);

        if (!$bird) {
            throw $this->createNotFoundException('Bird not found');
        }

        return $this->render('bird/show.html.twig', [
            'bird' => $bird
        ]);
    }

}
