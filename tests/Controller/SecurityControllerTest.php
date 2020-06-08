<?php

namespace App\Tests\Controller;

use App\Tests\AppTestCase;
use Symfony\Component\DomCrawler\Crawler;

final class SecurityControllerTest extends AppTestCase
{
    public function testLoginSuccessful(): void
    {
        $crawler = $this->successfullyLoadLoginPage();

        $this->fillAndSubmitLoginForm($crawler, 'hello@lionel-d.com', 'password');

        $this->assertResponseRedirects('/app/dashboard');
        $this->kernelBrowser->followRedirect();
        $this->assertSelectorTextContains('h1', 'Welcome Lionel!');
    }

    public function testLogout(): void
    {
        $this->assertLoggedAsUser();

        $this->kernelBrowser->request('GET', '/logout');

        $this->assertResponseRedirects();
        $this->kernelBrowser->followRedirect();
        $this->assertSelectorTextContains('h1', 'This is Nestor');
    }

    public function testLoginAsAlreadyAuthenticated(): void
    {
        $this->assertLoggedAsUser();

        $this->kernelBrowser->request('GET', '/login');

        $this->assertResponseRedirects('/app/dashboard');
        $this->kernelBrowser->followRedirect();
        $this->assertSelectorTextContains('h1', 'Welcome Lionel!');
    }

    private function successfullyLoadLoginPage(): Crawler
    {
        $crawler = $this->kernelBrowser->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Please sign in');

        return $crawler;
    }

    private function fillAndSubmitLoginForm(Crawler $crawler, string $email, string $password): void
    {
        $form = $crawler->selectButton('login_submit')->form();

        $form['email'] = $email;
        $form['password'] = $password;

        $this->kernelBrowser->submit($form);
    }
}
