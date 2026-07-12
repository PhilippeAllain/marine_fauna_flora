<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\MolluscRepository;
use App\Model\SearchData;
use App\Form\SearchType;


#[Route('/mollusc', name: 'mollusc.', methods: ['GET', 'POST'])]

final class MolluscController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, MolluscRepository $molluscRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $molluscs = $molluscRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('mollusc/index.html.twig', [
                'form' => $form,
                'molluscs' => $molluscs
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $molluscs = $molluscRepository->paginateMolluscs($page);
        return $this->render('mollusc/index.html.twig', [
            'form' => $form,
            'molluscs' => $molluscs,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, MolluscRepository $molluscRepository): Response
    {
        $mollusc = $molluscRepository->find($id);

        if (!$mollusc) {
            throw $this->createNotFoundException('Mollusc not found');
        }

        return $this->render('mollusc/show.html.twig', [
            'mollusc' => $mollusc
        ]);
    }

}
