<?php
// src/Controller/AdminProductImageController.php
namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductImage;
use App\Service\ProductImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/product')]
#[IsGranted('ROLE_ADMIN')]
class AdminProductImageController extends AbstractController
{
    private $productImageUploader;

    public function __construct(ProductImageUploader $productImageUploader)
    {
        $this->productImageUploader = $productImageUploader;
    }

    #[Route('/{id}/images', name: 'admin_product_images')]
    public function manageImages(Product $product): Response
    {
        return $this->render('admin/products/images.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/images/add', name: 'admin_product_image_add', methods: ['POST'])]
    public function addImage(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        $file = $request->files->get('file');
        $position = $request->request->get('position', 0);
        
        if ($file) {
            $fileName = $this->productImageUploader->upload($file);
            
            $image = new ProductImage();
            $image->setFilename($fileName);
            $image->setProduct($product);
            $image->setPosition((int)$position);
            
            $entityManager->persist($image);
            $entityManager->flush();
            
            $this->addFlash('success', 'Image ajoutée avec succès!');
        } else {
            $this->addFlash('error', 'Veuillez sélectionner une image.');
        }

        return $this->redirectToRoute('admin_product_images', ['id' => $product->getId()]);
    }

    #[Route('/image/{id}/delete', name: 'admin_product_image_delete', methods: ['POST'])]
    public function deleteImage(ProductImage $image, EntityManagerInterface $entityManager): Response
    {
        $productId = $image->getProduct()->getId();
        
        // Supprimer le fichier physique
        $this->productImageUploader->delete($image->getFilename());
        
        // Supprimer l'entité
        $entityManager->remove($image);
        $entityManager->flush();
        
        $this->addFlash('success', 'Image supprimée avec succès!');
        
        return $this->redirectToRoute('admin_product_images', ['id' => $productId]);
    }

    #[Route('/image/{id}/move-up', name: 'admin_product_image_move_up', methods: ['POST'])]
    public function moveImageUp(ProductImage $image, EntityManagerInterface $entityManager): Response
    {
        $currentPosition = $image->getPosition();
        $image->setPosition($currentPosition - 1);
        
        $entityManager->flush();
        
        return $this->redirectToRoute('admin_product_images', ['id' => $image->getProduct()->getId()]);
    }

    #[Route('/image/{id}/move-down', name: 'admin_product_image_move_down', methods: ['POST'])]
    public function moveImageDown(ProductImage $image, EntityManagerInterface $entityManager): Response
    {
        $currentPosition = $image->getPosition();
        $image->setPosition($currentPosition + 1);
        
        $entityManager->flush();
        
        return $this->redirectToRoute('admin_product_images', ['id' => $image->getProduct()->getId()]);
    }
}