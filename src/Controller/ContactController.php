<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Event\ContactRequestEvent;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, EventDispatcherInterface $dispatcher): Response
    {
        $data = new ContactDTO(); // initialisation des données (objet vide)

        $form = $this->createForm(ContactType::class, $data); // création de form & on lui passe des données
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $dispatcher->dispatch(new ContactRequestEvent($data));
                $this->addFlash('success', 'Votre email a bien été envoyé');
                return $this->redirectToRoute('contact');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Impossible d\'envoyer votre email');
            }

            /*
            $mail = (new TemplatedEmail())
                ->to($data->service)
                ->from($data->email)
                ->subject('Demande de contact')
                ->htmlTemplate('emails/contact.html.twig')
                ->context(['data' => $data]);
            try {
                $mailer->send($mail);
                $this->addFlash('success', 'Votre email a bien été envoyé');
                return $this->redirectToRoute('contact');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Impossible d\'envoyer votre email');
            }
                */
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
