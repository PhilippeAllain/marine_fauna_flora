<?php

namespace App\Controller;

use App\Repository\SeaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
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
            $seas = Pagerfanta::createForCurrentPageWithMaxPerPage(
                new QueryAdapter($seaRepository->findBySearch($searchData)),
                $request->query->get(key: 'page', default: 1),
                maxPerPage: 1
            );

            return $this->render('sea/index.html.twig', [
                'form' => $form->createView(),
                'seas' => $seas
            ]);
        }

        $seas = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($seaRepository->findByName()),
            $request->query->get(key: 'page', default: 1),
            maxPerPage: 2
        );

        return $this->render('sea/index.html.twig', [
            'form' => $form->createView(),
            'seas' => $seas
        ]);
        //dd($request);
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
