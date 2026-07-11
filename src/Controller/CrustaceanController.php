<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\CrustaceanRepository;
use App\Model\SearchData;
use App\Form\SearchType;


#[Route('/crustacean', name: 'crustacean.', methods: ['GET', 'POST'])]

final class CrustaceanController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, CrustaceanRepository $crustaceanRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $crustaceans = $crustaceanRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('crustacean/index.html.twig', [
                'form' => $form,
                'crustaceans' => $crustaceans
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $crustaceans = $crustaceanRepository->paginateCrustaceans($page);
        return $this->render('crustacean/index.html.twig', [
            'form' => $form,
            'crustaceans' => $crustaceans,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, CrustaceanRepository $crustaceanRepository): Response
    {
        $crustacean = $crustaceanRepository->find($id);

        if (!$crustacean) {
            throw $this->createNotFoundException('Crustacean not found');
        }

        return $this->render('crustacean/show.html.twig', [
            'crustacean' => $crustacean
        ]);
    }

}
