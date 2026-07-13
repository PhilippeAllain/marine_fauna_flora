<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\CrustaceanRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Crustacean;
use App\Form\CrustaceanType;
use App\Model\SearchData;
use App\Form\SearchType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/crustacean', name: 'admin.crustacean.', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
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
            return $this->render('/admin/crustacean/index.html.twig', [
                'form' => $form,
                'crustaceans' => $crustaceans
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $crustaceans = $crustaceanRepository->paginateCrustaceans($page);
        return $this->render('/admin/crustacean/index.html.twig', [
            'form' => $form,
            'crustaceans' => $crustaceans,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $crustacean = new Crustacean();
        $form = $this->createForm(CrustaceanType::class, $crustacean);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($crustacean);
            $em->flush();
            $this->addFlash('success', 'Crustace créé avec succès !');
            return $this->redirectToRoute('admin.crustacean.index');
        }
        return $this->render('admin/crustacean/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, CrustaceanRepository $crustaceanRepository): Response
    {
        $crustacean = $crustaceanRepository->find($id);

        if (!$crustacean) {
            throw $this->createNotFoundException('Crustacean not found');
        }

        return $this->render('admin/crustacean/show.html.twig', [
            'crustacean' => $crustacean
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Crustacean $crustacean, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CrustaceanType::class, $crustacean);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('success', 'Le crustacé a été modifié avec succès !');
            return $this->redirectToRoute('admin.crustacean.index');
        }

        return $this->render('admin/crustacean/edit.html.twig', [
            'crustacean' => $crustacean,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Crustacean $crustacean, EntityManagerInterface $em): Response
    {
        $em->remove($crustacean);
        $em->flush();
        $this->addFlash('success', 'Le crustacé a bien été supprimé');
        return $this->redirectToRoute('admin.crustacean.index');
    }
}
