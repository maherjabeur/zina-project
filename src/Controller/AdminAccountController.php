<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminAccountController extends AbstractController
{
    #[Route('/profile/change-password', name: 'profile_change_password', methods: ['GET', 'POST'])]
    #[Route('/admin/account/change-password', name: 'admin_change_password', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('change_password', (string) $request->request->get('_token'))) {
                $this->addFlash('danger', $this->localizedMessage($request, 'Action non autorisee.', 'عملية غير مسموح بها.'));

                return $this->redirectToCurrentPasswordRoute($request);
            }

            $currentPassword = (string) $request->request->get('current_password', '');
            $newPassword = (string) $request->request->get('new_password', '');
            $confirmPassword = (string) $request->request->get('confirm_password', '');

            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('danger', $this->localizedMessage($request, 'Le mot de passe actuel est incorrect.', 'كلمة المرور الحالية غير صحيحة.'));

                return $this->redirectToCurrentPasswordRoute($request);
            }

            if (strlen($newPassword) < 8) {
                $this->addFlash('danger', $this->localizedMessage($request, 'Le nouveau mot de passe doit contenir au moins 8 caracteres.', 'يجب أن تحتوي كلمة المرور الجديدة على 8 أحرف على الأقل.'));

                return $this->redirectToCurrentPasswordRoute($request);
            }

            if ($newPassword !== $confirmPassword) {
                $this->addFlash('danger', $this->localizedMessage($request, 'La confirmation du mot de passe ne correspond pas.', 'تأكيد كلمة المرور غير مطابق.'));

                return $this->redirectToCurrentPasswordRoute($request);
            }

            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $entityManager->flush();

            $this->addFlash('success', $this->localizedMessage($request, 'Votre mot de passe a ete modifie avec succes.', 'تم تغيير كلمة المرور بنجاح.'));

            return $this->isAdminPasswordRoute($request)
                ? $this->redirectToRoute('admin_dashboard')
                : $this->redirectToRoute('home');
        }

        return $this->render('admin/account/change_password.html.twig');
    }

    private function redirectToCurrentPasswordRoute(Request $request): Response
    {
        return $this->redirectToRoute($this->isAdminPasswordRoute($request) ? 'admin_change_password' : 'profile_change_password');
    }

    private function isAdminPasswordRoute(Request $request): bool
    {
        return str_starts_with((string) $request->attributes->get('_route', ''), 'admin_');
    }

    private function localizedMessage(Request $request, string $fr, string $ar): string
    {
        return $request->getLocale() === 'ar' ? $ar : $fr;
    }
}
