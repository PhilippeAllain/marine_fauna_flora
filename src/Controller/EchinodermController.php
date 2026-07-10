<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\EchinodermRepository;
use App\Model\SearchData;
use App\Form\SearchType;


#[Route('/echinoderm', name: 'echinoderm.', methods: ['GET', 'POST'])]

final class EchinodermController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, EchinodermRepository $echinodermRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $echinoderms = $echinodermRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('echinoderm/index.html.twig', [
                'form' => $form,
                'echinoderms' => $echinoderms
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $echinoderms = $echinodermRepository->paginateEchinoderms($page);
        return $this->render('echinoderm/index.html.twig', [
            'form' => $form,
            'echinoderms' => $echinoderms,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, EchinodermRepository $echinodermRepository): Response
    {
        $echinoderm = $echinodermRepository->find($id);

        if (!$echinoderm) {
            throw $this->createNotFoundException('Echinoderm not found');
        }

        return $this->render('echinoderm/show.html.twig', [
            'echinoderm' => $echinoderm
        ]);
    }

}
