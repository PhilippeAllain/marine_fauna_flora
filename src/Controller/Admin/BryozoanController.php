<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\BryozoanRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Bryozoan;
use App\Form\BryozoanType;
use App\Model\SearchData;
use App\Form\SearchType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/bryozoan', name: 'admin.bryozoan.', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
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
            return $this->render('/admin/bryozoan/index.html.twig', [
                'form' => $form,
                'bryozoans' => $bryozoans
            ]);
        }
        $page = $request->query->getInt('page', 1);
        $bryozoans = $bryozoanRepository->paginateBryozoans($page);
        return $this->render('/admin/bryozoan/index.html.twig', [
            'form' => $form,
            'bryozoans' => $bryozoans,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $bryozoan = new Bryozoan();
        $form = $this->createForm(BryozoanType::class, $bryozoan);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($bryozoan);
            $em->flush();
            $this->addFlash('success', 'Bryozoan créé avec succès !');
            return $this->redirectToRoute('admin.bryozoan.index');
        }
        return $this->render('admin/bryozoan/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, BryozoanRepository $bryozoanRepository): Response
    {
        $bryozoan = $bryozoanRepository->find($id);

        if (!$bryozoan) {
            throw $this->createNotFoundException('Bryozoan not found');
        }

        return $this->render('admin/bryozoan/show.html.twig', [
            'bryozoan' => $bryozoan
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Bryozoan $bryozoan, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BryozoanType::class, $bryozoan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('success', 'Le bryozoan a été modifié avec succès !');
            return $this->redirectToRoute('admin.bryozoan.index');
        }

        return $this->render('admin/bryozoan/edit.html.twig', [
            'bryozoan' => $bryozoan,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Bryozoan $bryozoan, EntityManagerInterface $em): Response
    {
        $em->remove($bryozoan);
        $em->flush();
        $this->addFlash('success', 'Le bryozoan a bien été supprimé');
        return $this->redirectToRoute('admin.bryozoan.index');
    }
}
