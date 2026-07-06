<?php

namespace App\Controller;

use App\Repository\SeaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Model\SearchData;
use App\Form\SearchType;


#[Route('/sea', name: 'sea.', methods: ['GET', 'POST'])]
final class SeaController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, SeaRepository $seaRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $seas = $seaRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('sea/index.html.twig', [
                'form' => $form,
                'seas' => $seas
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $seas = $seaRepository->paginateSeas($page);
        return $this->render('sea/index.html.twig', [
            'form' => $form,
            'seas' => $seas,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, SeaRepository $seaRepository): Response
    {
        $sea = $seaRepository->find($id);

        if (!$sea) {
            throw $this->createNotFoundException('Sea not found');
        }

        return $this->render('sea/show.html.twig', [
            'sea' => $sea
        ]);
    }
}
