<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\BryozoanRepository;
use App\Model\SearchData;
use App\Form\SearchType;


#[Route('/bryozoan', name: 'bryozoan.', methods: ['GET', 'POST'])]

final class BryozoanController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, BryozoanRepository $bryozoanRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $bryozoans = $bryozoanRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('bryozoan/index.html.twig', [
                'form' => $form,
                'bryozoans' => $bryozoans
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $bryozoans = $bryozoanRepository->paginateBryozoans($page);
        return $this->render('bryozoan/index.html.twig', [
            'form' => $form,
            'bryozoans' => $bryozoans,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, BryozoanRepository $bryozoanRepository): Response
    {
        $bryozoan = $bryozoanRepository->find($id);

        if (!$bryozoan) {
            throw $this->createNotFoundException('Bryozoan not found');
        }

        return $this->render('bryozoan/show.html.twig', [
            'bryozoan' => $bryozoan
        ]);
    }

}
