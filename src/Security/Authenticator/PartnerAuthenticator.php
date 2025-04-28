<?php
namespace App\Security\Authenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class PartnerAuthenticator extends AbstractLoginFormAuthenticator
{
    private UserProviderInterface $userProvider;
    private UrlGeneratorInterface $urlGenerator;
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        UserProviderInterface $userProvider,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->userProvider = $userProvider;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function supports(Request $request): bool
    {
        return $request->getPathInfo() === '/login/partner' && $request->isMethod('POST');
    }

public function authenticate(Request $request): Passport
{
    // Récupérer les données spécifiques au formulaire
    $formData = $request->request->all()['login_partner_form'] ?? null;

    // Debug : Vérifiez les données envoyées
    dump($formData);

    // Vérifiez que $formData est bien un tableau
    if (!is_array($formData)) {
        throw new AuthenticationException('Données du formulaire invalides.');
    }

    // Vérifiez la validité du token CSRF
    $csrfToken = $formData['_token'] ?? null;
    if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('login_partner', $csrfToken))) {
        throw new AuthenticationException('Jeton CSRF invalide.');
    }

    // Récupérer l'email et le mot de passe
    $email = $formData['email'] ?? null;
    $password = $formData['password'] ?? null;

    if (!$email || !$password) {
        throw new AuthenticationException('Email ou mot de passe manquant.');
    }

    // Créer un Passport pour l'authentification
    return new Passport(
        new UserBadge($email, function ($userIdentifier) {
            $user = $this->userProvider->loadUserByIdentifier($userIdentifier);
            if (!$user) {
                throw new UserNotFoundException('Utilisateur introuvable.');
            }

            return $user;
        }),
        new PasswordCredentials($password)
    );
}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): Response
    {
        return new RedirectResponse($this->urlGenerator->generate('app_dashboard_partenaire'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
{
    $errorMessage = $exception->getMessage();

    // Adapter les messages d'erreur en fonction du type d'exception
    if (str_contains($errorMessage, 'Utilisateur introuvable') || str_contains($errorMessage, 'User not found')) {
        $message = 'Cet email n\'est associé à aucun compte partenaire. Veuillez vérifier votre adresse email.';
    } elseif (str_contains($errorMessage, 'Invalid credentials')) {
        $message = 'Mot de passe incorrect. Vérifiez que vous avez saisi le bon mot de passe.';
    } elseif (str_contains($errorMessage, 'Email ou mot de passe manquant')) {
        $message = 'Veuillez remplir tous les champs, y compris l\'email et le mot de passe.';
    } elseif (str_contains($errorMessage, 'Jeton CSRF invalide')) {
        $message = 'Le formulaire a expiré ou est invalide. Veuillez réessayer.';
    } else {
        $message = 'Échec de l\'authentification. Veuillez vérifier vos identifiants ou réessayer plus tard.';
    }

    // Ajout du message d'erreur à la session
    $request->getSession()->getFlashBag()->add('error', $message);

    return new RedirectResponse($this->getLoginUrl($request));
}


    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('app_login_controller_partner');
    }
} 
