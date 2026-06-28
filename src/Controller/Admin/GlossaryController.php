<?php

namespace App\Controller\Admin;

use App\Repository\GlossaryRepository;
use App\Entity\Glossary;
use App\Form\GlossaryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Model\SearchData;
use App\Form\SearchType;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/glossary', name: 'admin.glossary.', methods: ['GET'])]
#[IsGranted('ROLE_ADMIN')]
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
            return $this->render('admin/glossary/index.html.twig', [
                'form' => $form,
                'glossaries' => $glossaries,

            ]);
        }
        $page = $request->query->getInt('page', 1);
        $glossaries = $glossaryRepository->paginateGlossaries($page);
        return $this->render('admin/glossary/index.html.twig', [
            'form' => $form,
            'glossaries' => $glossaries,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        // Logic to handle form submission and create a new glossary entry

        $glossary = new Glossary();
        $form = $this->createForm(GlossaryType::class, $glossary);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($glossary);
            $em->flush();
            $this->addFlash('success', 'Terme du glossaire créé avec succès !');
            return $this->redirectToRoute('admin.glossary.index');
        }
        return $this->render('admin/glossary/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, GlossaryRepository $glossaryRepository): Response
    {
        $glossary = $glossaryRepository->find($id);

        if (!$glossary) {
            throw $this->createNotFoundException('Glossary not found');
        }

        return $this->render('admin/glossary/show.html.twig', [
            'glossary' => $glossary
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Glossary $glossary, Request $request, EntityManagerInterface $em): Response
    {
        // dd($glossary);
        $form = $this->createForm(GlossaryType::class, $glossary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Le terme du glossaire a été modifié avec succès !');
            return $this->redirectToRoute('admin.glossary.index');
        }

        return $this->render('admin/glossary/edit.html.twig', [
            'glossary' => $glossary,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Glossary $glossary, EntityManagerInterface $em): Response
    {
        $em->remove($glossary);
        $em->flush();
        $this->addFlash('success', 'Le terme du glossaire a bien été supprimé');
        return $this->redirectToRoute('admin.glossary.index');
    }
}
