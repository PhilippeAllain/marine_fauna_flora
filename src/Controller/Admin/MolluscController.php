<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\MolluscRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Mollusc;
use App\Form\MolluscType;
use App\Model\SearchData;
use App\Form\SearchType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/mollusc', name: 'admin.mollusc.', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
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
            return $this->render('/admin/mollusc/index.html.twig', [
                'form' => $form,
                'molluscs' => $molluscs
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $molluscs = $molluscRepository->paginateMolluscs($page);
        return $this->render('/admin/mollusc/index.html.twig', [
            'form' => $form,
            'molluscs' => $molluscs,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $mollusc = new Mollusc();
        $form = $this->createForm(MolluscType::class, $mollusc);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($mollusc);
            $em->flush();
            $this->addFlash('success', 'Le mollusque créé avec succès !');
            return $this->redirectToRoute('admin.mollusc.index');
        }
        return $this->render('admin/mollusc/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, MolluscRepository $molluscRepository): Response
    {
        $mollusc = $molluscRepository->find($id);

        if (!$mollusc) {
            throw $this->createNotFoundException('Mollusc not found');
        }

        return $this->render('admin/mollusc/show.html.twig', [
            'mollusc' => $mollusc
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Mollusc $mollusc, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MolluscType::class, $mollusc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('success', 'Le mollusque a été modifié avec succès !');
            return $this->redirectToRoute('admin.mollusc.index');
        }

        return $this->render('admin/mollusc/edit.html.twig', [
            'mollusc' => $mollusc,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Mollusc $mollusc, EntityManagerInterface $em): Response
    {
        $em->remove($mollusc);
        $em->flush();
        $this->addFlash('success', 'Le mollusque a bien été supprimé');
        return $this->redirectToRoute('admin.mollusc.index');
    }
}
