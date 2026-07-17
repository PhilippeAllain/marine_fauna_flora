<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\RedRepository;
use App\Model\SearchData;
use App\Form\SearchType;


#[Route('/red', name: 'red.', methods: ['GET', 'POST'])]

final class RedController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, RedRepository $redRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $reds = $redRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('red/index.html.twig', [
                'form' => $form,
                'reds' => $reds
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $reds = $redRepository->paginateReds($page);
        return $this->render('red/index.html.twig', [
            'form' => $form,
            'reds' => $reds,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, RedRepository $redRepository): Response
    {
        $red = $redRepository->find($id);

        if (!$red) {
            throw $this->createNotFoundException('Red not found');
        }

        return $this->render('red/show.html.twig', [
            'red' => $red
        ]);
    }

}
