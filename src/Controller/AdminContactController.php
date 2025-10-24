<?php
// src/Controller/AdminContactController.php
namespace App\Controller;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/contacts')]
#[IsGranted('ROLE_ADMIN')]
class AdminContactController extends AbstractController
{
    #[Route('/', name: 'admin_contacts')]
    public function index(ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findBy([], ['createdAt' => 'DESC']);
        $unreadCount = $contactRepository->findUnreadCount();

        return $this->render('admin/contacts/index.html.twig', [
            'contacts' => $contacts,
            'unreadCount' => $unreadCount,
        ]);
    }

    #[Route('/{id}', name: 'admin_contact_show')]
    public function show(Contact $contact, EntityManagerInterface $entityManager): Response
    {
        // Marquer comme lu
        if (!$contact->isRead()) {
            $contact->setIsRead(true);
            $entityManager->flush();
        }

        return $this->render('admin/contacts/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_contact_delete', methods: ['POST'])]
    public function delete(Contact $contact, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($contact);
        $entityManager->flush();

        $this->addFlash('success', 'Message supprimé avec succès!');

        return $this->redirectToRoute('admin_contacts');
    }
}