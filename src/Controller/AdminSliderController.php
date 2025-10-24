<?php
// src/Controller/AdminSliderController.php
namespace App\Controller;

use App\Entity\SliderImage;
use App\Form\SliderImageType;
use App\Repository\OrderRepository;
use App\Repository\SliderImageRepository;
use App\Service\SliderImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/slider')]
#[IsGranted('ROLE_ADMIN')]
class AdminSliderController extends AbstractController
{
    private $sliderUploader;

    public function __construct(SliderImageUploader $sliderUploader)
    {
        $this->sliderUploader = $sliderUploader;
    }

    #[Route('/', name: 'admin_slider')]
    public function index(SliderImageRepository $sliderImageRepository,OrderRepository $orderRepository): Response
    {
        $sliderImages = $sliderImageRepository->findBy([], ['position' => 'ASC']);
        $recentOrders = $orderRepository->findRecentOrders(10);

        return $this->render('admin/slider/index.html.twig', [
            'sliderImages' => $sliderImages,
            'recentOrders' => $recentOrders,
        ]);
    }

    #[Route('/new', name: 'admin_slider_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sliderImage = new SliderImage();
        $form = $this->createForm(SliderImageType::class, $sliderImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->get('file')->getData();
            
            if ($file) {
                $fileName = $this->sliderUploader->upload($file);
                $sliderImage->setFilename($fileName);
                
                $entityManager->persist($sliderImage);
                $entityManager->flush();
                
                $this->addFlash('success', 'Image du slider ajoutée avec succès!');
                return $this->redirectToRoute('admin_slider');
            } else {
                $this->addFlash('error', 'Veuillez sélectionner une image.');
            }
        }

        return $this->render('admin/slider/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_slider_edit')]
    public function edit(SliderImage $sliderImage, Request $request, EntityManagerInterface $entityManager): Response
    {
        $oldFilename = $sliderImage->getFilename();
        
        $form = $this->createForm(SliderImageType::class, $sliderImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->get('file')->getData();
            
            if ($file) {
                // Supprimer l'ancien fichier
                if ($oldFilename) {
                    $this->sliderUploader->delete($oldFilename);
                }
                
                // Uploader le nouveau fichier
                $fileName = $this->sliderUploader->upload($file);
                $sliderImage->setFilename($fileName);
            }
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Image du slider modifiée avec succès!');
            return $this->redirectToRoute('admin_slider');
        }

        return $this->render('admin/slider/edit.html.twig', [
            'form' => $form->createView(),
            'sliderImage' => $sliderImage,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_slider_delete', methods: ['POST'])]
    public function delete(SliderImage $sliderImage, EntityManagerInterface $entityManager): Response
    {
        // Supprimer le fichier physique
        if ($sliderImage->getFilename()) {
            $this->sliderUploader->delete($sliderImage->getFilename());
        }
        
        // Supprimer l'entité
        $entityManager->remove($sliderImage);
        $entityManager->flush();
        
        $this->addFlash('success', 'Image du slider supprimée avec succès!');
        
        return $this->redirectToRoute('admin_slider');
    }

    #[Route('/{id}/toggle', name: 'admin_slider_toggle', methods: ['POST'])]
    public function toggle(SliderImage $sliderImage, EntityManagerInterface $entityManager): Response
    {
        $sliderImage->setIsActive(!$sliderImage->isActive());
        $entityManager->flush();
        
        $status = $sliderImage->isActive() ? 'activée' : 'désactivée';
        $this->addFlash('success', "Image {$status} avec succès!");
        
        return $this->redirectToRoute('admin_slider');
    }
}