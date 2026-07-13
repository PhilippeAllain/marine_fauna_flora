<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\CnidarianRepository;
use App\Model\SearchData;
use App\Form\SearchType;


#[Route('/cnidarian', name: 'cnidarian.', methods: ['GET', 'POST'])]

final class CnidarianController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, CnidarianRepository $cnidarianRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $cnidarians = $cnidarianRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('cnidarian/index.html.twig', [
                'form' => $form,
                'cnidarians' => $cnidarians
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $cnidarians = $cnidarianRepository->paginateCnidarians($page);
        return $this->render('cnidarian/index.html.twig', [
            'form' => $form,
            'cnidarians' => $cnidarians,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, CnidarianRepository $cnidarianRepository): Response
    {
        $cnidarian = $cnidarianRepository->find($id);

        if (!$cnidarian) {
            throw $this->createNotFoundException('Cnidarian not found');
        }

        return $this->render('cnidarian/show.html.twig', [
            'cnidarian' => $cnidarian
        ]);
    }

}
