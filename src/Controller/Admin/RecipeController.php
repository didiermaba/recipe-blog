<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[route("/admin/recettes", name: "admin.recipe.")]
class RecipeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(RecipeRepository $repository): Response
    {
        $recipes = $repository->findAll();
        // dd($recipes);
        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Route('/duration', name: 'duration')]
    public function duration(Request $request, RecipeRepository $repository): Response
    {
        $recipes = $repository->findWithDurationLowerThan(16);
        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    // route créée en fx de besoin grace au query builder ds la class repository 
    // #[Route('/modify', name: 'modify')]
    // public function modify(Request $request, RecipeRepository $repository, EntityManagerInterface $em): Response
    // {
    //     $recipes = $repository->findWithDurationLowerThan(16);
    //     $recipes[0]->setTitle('Pates bolognaise');
    //     $em->flush();
    //     return $this->render('recipe/index.html.twig', [
    //         'recipes' => $recipes
    //     ]);
    // }

    // #[Route('/crea', name: 'recipe.crea')]
    // public function crea(Request $request, RecipeRepository $repository, EntityManagerInterface $em): Response
    // {
    //     $recipes = $repository->findWithDurationLowerThan(16);

    //     $recipe = new Recipe();
    //     $recipe->setTitle('barbe à papa')
    //         ->setSlug('barbe-papa')
    //         ->setContent('Pause repas à l\'italienne avec ces spaghetti à la bolognaise. Un concentré de saveurs composé d’une viande de bœuf hachée agrémentée de tomates, d\'oignons et de basilic. Prêt en quelques minutes au micro-ondes, ce plat express donne une note chaleureuse à vos déjeuners ou dîners solo.||1 part\n\nPause repas à l\'italienne avec ces spaghetti à la bolognaise. Un concentré de saveurs composé d’une viande de bœuf hachée agrémentée de tomates, d\'oignons et de basilic. Prêt en quelques minutes au micro-ondes, ce plat express donne une note chaleureuse à vos déjeuners ou dîners solo.')
    //         ->setDuration(5)
    //         ->setCreatedAt(new \DateTimeImmutable())
    //         ->setUpdatedAt(new \DateTimeImmutable());
    //     $em->persist($recipe); // pr suivre l'objet crée
    //     $em->flush(); // pr memoriser les infos en bd
    //     return $this->render('recipe/index.html.twig', [
    //         'recipes' => $recipes
    //     ]);
    // }

    // #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    // public function show(Request $request, string $slug, int $id, RecipeRepository $repository): Response
    // {
    //     $recipe = $repository->find($id);
    //     if ($recipe->getSlug() !== $slug) {
    //         return $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
    //     }

    //     return $this->render('recipe/show.html.twig', [
    //         'recipe' => $recipe,
    //     ]);
    // }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em, FormFactoryInterface $formFactory )
    {
        $form = $formFactory->create(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $recipe->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();
            $this->addFlash('success', 'la recette a bien été modifiée');
            return $this->redirectToRoute('admin/recipe.index');
        }

        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $recipe->setCreatedAt(new \DateTimeImmutable());
            // $recipe->setUpdatedAt(new \DateTimeImmutable());
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'Votre recette a bien été enregistré');
            return $this->redirectToRoute('admin/recipe.index');
        }
        return $this->render('admin/recipe/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Recipe $recipe, EntityManagerInterface $em) {

        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'La recette a bien été supprimée');
        return $this->redirectToRoute('admin/recipe.index');
    }
}
