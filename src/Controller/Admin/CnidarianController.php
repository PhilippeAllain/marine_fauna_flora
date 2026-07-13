<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\CnidarianRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Cnidarian;
use App\Form\CnidarianType;
use App\Model\SearchData;
use App\Form\SearchType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/cnidarian', name: 'admin.cnidarian.', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
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
            return $this->render('/admin/cnidarian/index.html.twig', [
                'form' => $form,
                'cnidarians' => $cnidarians
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $cnidarians = $cnidarianRepository->paginateCnidarians($page);
        return $this->render('/admin/cnidarian/index.html.twig', [
            'form' => $form,
            'cnidarians' => $cnidarians,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $cnidarian = new Cnidarian();
        $form = $this->createForm(CnidarianType::class, $cnidarian);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($cnidarian);
            $em->flush();
            $this->addFlash('success', 'Cnidarian créé avec succès !');
            return $this->redirectToRoute('admin.cnidarian.index');
        }
        return $this->render('admin/cnidarian/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, CnidarianRepository $cnidarianRepository): Response
    {
        $cnidarian = $cnidarianRepository->find($id);

        if (!$cnidarian) {
            throw $this->createNotFoundException('Cnidarian not found');
        }

        return $this->render('admin/cnidarian/show.html.twig', [
            'cnidarian' => $cnidarian
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Cnidarian $cnidarian, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CnidarianType::class, $cnidarian);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('success', 'Le cnidaire a été modifié avec succès !');
            return $this->redirectToRoute('admin.cnidarian.index');
        }

        return $this->render('admin/cnidarian/edit.html.twig', [
            'cnidarian' => $cnidarian,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Cnidarian $cnidarian, EntityManagerInterface $em): Response
    {
        $em->remove($cnidarian);
        $em->flush();
        $this->addFlash('success', 'Le cnidaria a bien été supprimé');
        return $this->redirectToRoute('admin.cnidarian.index');
    }
}
