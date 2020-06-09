<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

abstract class AppTestCase extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    protected $kernelBrowser;
    /**
     * @var EntityManager
     */
    protected $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kernelBrowser = static::createClient();

        if (
            null !== $this->kernelBrowser->getContainer() &&
            null !== $this->kernelBrowser->getContainer()->get('doctrine') &&
            method_exists($this->kernelBrowser->getContainer()->get('doctrine'), 'getManager')
        ) {
            $this->entityManager = $this->kernelBrowser->getContainer()
                ->get('doctrine')
                ->getManager();
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        // @phpstan-ignore-next-line
        $this->entityManager = null; // avoid memory leaks
    }

    protected function assertLoggedAsUser(): void
    {
        $this->assertLogged('hello@lionel-d.com', ['ROLE_USER']);
    }

    /**
     * @param string   $email
     * @param string[] $roles
     */
    private function assertLogged($email, array $roles): void
    {
        /** @var UserInterface $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (null !== $this->kernelBrowser->getContainer()) {
            /** @var Session|mixed[] $session */
            $session = $this->kernelBrowser->getContainer()->get('session');

            $firewall = 'main';

            $token = new PostAuthenticationGuardToken($user, '_security_'.$firewall, $roles);

            $session->set('_security_'.$firewall, serialize($token));
            $session->save();

            $cookie = new Cookie($session->getName(), $session->getId());

            $this->kernelBrowser->getCookieJar()->set($cookie);
        }
    }
}
