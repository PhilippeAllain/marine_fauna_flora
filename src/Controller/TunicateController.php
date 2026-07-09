<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\TunicateRepository;
use App\Model\SearchData;
use App\Form\SearchType;


#[Route('/tunicate', name: 'tunicate.', methods: ['GET', 'POST'])]

final class TunicateController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, TunicateRepository $tunicateRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $tunicates = $tunicateRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('tunicate/index.html.twig', [
                'form' => $form,
                'tunicates' => $tunicates
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $tunicates = $tunicateRepository->paginateTunicates($page);
        return $this->render('tunicate/index.html.twig', [
            'form' => $form,
            'tunicates' => $tunicates,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, TunicateRepository $tunicateRepository): Response
    {
        $tunicate = $tunicateRepository->find($id);

        if (!$tunicate) {
            throw $this->createNotFoundException('Tunicate not found');
        }

        return $this->render('tunicate/show.html.twig', [
            'tunicate' => $tunicate
        ]);
    }

}
