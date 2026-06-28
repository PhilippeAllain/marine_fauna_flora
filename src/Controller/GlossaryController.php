<?php

namespace App\Controller;

use App\Repository\GlossaryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Model\SearchData;
use App\Form\SearchType;


#[Route('/glossary', name: 'glossary.', methods: ['GET', 'POST'])]
final class GlossaryController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, GlossaryRepository $glossaryRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(type: SearchType::class, data: $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt(key: 'page', default: 1);
            $glossaries = $glossaryRepository->findBySearch($searchData, $searchData->page, limit: 2);
            return $this->render('glossary/index.html.twig', [
                'form' => $form,
                'glossaries' => $glossaries,

            ]);
        }
        $page = $request->query->getInt('page', 1);
        $glossaries = $glossaryRepository->paginateGlossaries($page);
        return $this->render('glossary/index.html.twig', [
            'form' => $form,
            'glossaries' => $glossaries,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, GlossaryRepository $glossaryRepository): Response
    {
        $glossary = $glossaryRepository->find($id);

        if (!$glossary) {
            throw $this->createNotFoundException('Glossary not found');
        }

        return $this->render('glossary/show.html.twig', [
            'glossary' => $glossary
        ]);
    }
}
