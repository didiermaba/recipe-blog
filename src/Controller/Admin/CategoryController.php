<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/category', name: 'admin.category.')]
#[IsGranted('ROLE_USER')]
class CategoryController extends AbstractController
{

    #[Route(name: 'index')]
    public function index(CategoryRepository $repository): Response
    {
        $repository = $repository->findAllWithCount();
        return $this->render('admin/category/index.html.twig', [
            'categories' => $repository
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash('succes', 'La catégorie a bien été créée');
            return $this->redirectToRoute('admin.category.index');
        }

        return $this->render('admin/category/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'edit', requirements: ["id" => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function edit(Category $category, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('succes', 'La catégorie a bien été modifiée');
            return $this->redirectToRoute('admin.category.index');
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form
        ]);
      
    }

    #[Route('/{id}', name: 'delete', requirements: ["id" => Requirement::DIGITS], methods: ['DELETE'])]
    public function delete(Category $category, EntityManagerInterface $em): Response
    {
        $em->remove($category);
        $em->flush();
        $this->addFlash('succes', 'La categorie a bien été supprimée');
        return $this->redirectToRoute('admin.category.index');
    }
}
