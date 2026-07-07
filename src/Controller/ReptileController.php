<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\ReptileRepository;
use App\Model\SearchData;
use App\Form\SearchType;


#[Route('/reptile', name: 'reptile.', methods: ['GET', 'POST'])]

final class ReptileController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, ReptileRepository $reptileRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $reptiles = $reptileRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('reptile/index.html.twig', [
                'form' => $form,
                'reptiles' => $reptiles
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $reptiles = $reptileRepository->paginateReptiles($page);
        return $this->render('reptile/index.html.twig', [
            'form' => $form,
            'reptiles' => $reptiles,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, ReptileRepository $reptileRepository): Response
    {
        $reptile = $reptileRepository->find($id);

        if (!$reptile) {
            throw $this->createNotFoundException('Reptile not found');
        }

        return $this->render('reptile/show.html.twig', [
            'reptile' => $reptile
        ]);
    }

}
