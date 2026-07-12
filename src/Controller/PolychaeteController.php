<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\PolychaeteRepository;
use App\Model\SearchData;
use App\Form\SearchType;


#[Route('/polychaete', name: 'polychaete.', methods: ['GET', 'POST'])]

final class PolychaeteController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, PolychaeteRepository $polychaeteRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $polychaetes = $polychaeteRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('polychaete/index.html.twig', [
                'form' => $form,
                'polychaetes' => $polychaetes
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $polychaetes = $polychaeteRepository->paginatePolychaetes($page);
        return $this->render('polychaete/index.html.twig', [
            'form' => $form,
            'polychaetes' => $polychaetes,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, PolychaeteRepository $polychaeteRepository): Response
    {
        $polychaete = $polychaeteRepository->find($id);

        if (!$polychaete) {
            throw $this->createNotFoundException('Polychète not found');
        }

        return $this->render('polychaete/show.html.twig', [
            'polychaete' => $polychaete
        ]);
    }

}
