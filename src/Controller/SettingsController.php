<?php
namespace App\Controller;

use App\Entity\Settings;
use App\Form\SettingsType;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    
    #[Route('/admin/settings', name: 'admin_settings')]
    public function index(Request $request, EntityManagerInterface $entityManager,SettingRepository $settingRepository)
    {
        $settings = $settingRepository->findOneBy([], ['id' => 'DESC']); // On suppose qu'il y a une seule configuration.

        if (!$settings) {
            // Créer une nouvelle configuration si elle n'existe pas.
            $settings = new Settings();
            $entityManager->persist($settings);
            $entityManager->flush();
        }

        $form = $this->createForm(SettingsType::class, $settings);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Les frais de livraison ont été mis à jour avec succès.');
        }

        return $this->render('admin/settings/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
