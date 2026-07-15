<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\GreenRepository;
use App\Model\SearchData;
use App\Form\SearchType;


#[Route('/green', name: 'green.', methods: ['GET', 'POST'])]

final class GreenController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, GreenRepository $greenRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $greens = $greenRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('green/index.html.twig', [
                'form' => $form,
                'greens' => $greens
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $greens = $greenRepository->paginateGreens($page);
        return $this->render('green/index.html.twig', [
            'form' => $form,
            'greens' => $greens,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, GreenRepository $greenRepository): Response
    {
        $green = $greenRepository->find($id);

        if (!$green) {
            throw $this->createNotFoundException('Green not found');
        }

        return $this->render('green/show.html.twig', [
            'green' => $green
        ]);
    }

}
