<?php

namespace App\Controller\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

class AuthController extends AbstractController
{
    #[Route(path: '/register', name: 'register')]
    public function register(): Response
    {
        return $this->render('auth/register.html.twig');
    }

    #[Route(path: '/forgot', name: 'forgot')]
    public function forgot(
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('_email');
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('error', 'No user found with this email address.');
                return $this->redirectToRoute('forgot');
            }

            $resetToken = Uuid::v4()->toRfc4122();
            $user->setResetToken($resetToken);
            $entityManager->flush();

            $mail = (new TemplatedEmail())
                ->from('no-reply@example.com')
                ->to($user->getEmail())
                ->subject('Password Reset Request')
                ->htmlTemplate('auth/reset.html.twig')
                ->context([
                    'resetToken' => $resetToken,
                    'userEmail' => $user->getEmail(),
                ]);

            $mailer->send($mail);

            $this->addFlash('success', 'An email has been sent to reset your password.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/forgot.html.twig');
    }

    #[Route(path: '/confirm', name: 'confirm')]
    public function confirm(): Response
    {
        return $this->render('auth/confirm.html.twig');
    }

    #[Route(path: '/reset/{token}', name: 'reset')]
    public function reset(
        string $token,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        CsrfTokenManagerInterface $csrfTokenManager,
    ): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Invalid or expired reset token.');
            return $this->redirectToRoute('forgot');
        }

        if ($request->isMethod('POST')) {
            $submittedCsrfToken = $request->request->get('_csrf_token');
            if (!$csrfTokenManager->isTokenValid(new CsrfToken('reset_password', $submittedCsrfToken))) {
                $this->addFlash('error', 'Invalid CSRF token.');
                return $this->redirectToRoute('reset', ['token' => $token]);
            }

            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');

            if ($password !== $confirmPassword) {
                $this->addFlash('error', 'Passwords do not match.');
                return $this->redirectToRoute('reset', ['token' => $token]);
            }

            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);
            $user->setResetToken(null); // Reset the token
            $entityManager->flush();

            $this->addFlash('success', 'Password successfully reset.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/reset.html.twig', [
            'token' => $token,
        ]);
    }
}
