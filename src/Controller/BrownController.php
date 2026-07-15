<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\BrownRepository;
use App\Model\SearchData;
use App\Form\SearchType;


#[Route('/brown', name: 'brown.', methods: ['GET', 'POST'])]

final class BrownController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, BrownRepository $brownRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $browns = $brownRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('brown/index.html.twig', [
                'form' => $form,
                'browns' => $browns
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $browns = $brownRepository->paginateBrowns($page);
        return $this->render('brown/index.html.twig', [
            'form' => $form,
            'browns' => $browns,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, BrownRepository $brownRepository): Response
    {
        $brown = $brownRepository->find($id);

        if (!$brown) {
            throw $this->createNotFoundException('Brown not found');
        }

        return $this->render('brown/show.html.twig', [
            'brown' => $brown
        ]);
    }

}
