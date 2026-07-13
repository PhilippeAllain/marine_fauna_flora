<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\SpongeRepository;
use App\Model\SearchData;
use App\Form\SearchType;


#[Route('/sponge', name: 'sponge.', methods: ['GET', 'POST'])]

final class SpongeController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, SpongeRepository $spongeRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $sponges = $spongeRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('sponge/index.html.twig', [
                'form' => $form,
                'sponges' => $sponges
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $sponges = $spongeRepository->paginateSponges($page);
        return $this->render('sponge/index.html.twig', [
            'form' => $form,
            'sponges' => $sponges,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, SpongeRepository $spongeRepository): Response
    {
        $sponge = $spongeRepository->find($id);

        if (!$sponge) {
            throw $this->createNotFoundException('Sponge not found');
        }

        return $this->render('sponge/show.html.twig', [
            'sponge' => $sponge
        ]);
    }

}
