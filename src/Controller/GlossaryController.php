<?php

namespace App\Controller;

use App\Repository\GlossaryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Doctrine\ORM\QueryAdapter;


#[Route('/glossary', name: 'glossary.', methods: ['GET'])]
final class GlossaryController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(Request $request, GlossaryRepository $glossaryRepository, EntityManagerInterface $em): Response
    {

        $glossaries = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($glossaryRepository->finndByName()),
            $request->query->get(key:'page', default: 1),
            maxPerPage: 2
        );

        return $this->render('glossary/index.html.twig', [
            'glossaries' => $glossaries
        ]);
        //dd($request);
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
