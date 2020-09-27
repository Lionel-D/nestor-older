<?php

namespace App\Tests\Controller;

use App\Tests\AppTestCase;
use Symfony\Component\DomCrawler\Crawler;

final class SecurityControllerTest extends AppTestCase
{
    public function testLoginFailedWrongPassword(): void
    {
        $crawler = $this->successfullyLoadLoginPage();

        $this->fillAndSubmitLoginForm($crawler, 'hello@lionel-d.com', 'wrongpassword');

        $this->assertResponseRedirects('/login');
        $this->kernelBrowser->followRedirect();
        $this->assertSelectorTextContains('.form-error-message', 'Invalid credentials.');
    }

    public function testLoginFailedNoAccount(): void
    {
        $crawler = $this->successfullyLoadLoginPage();

        $this->fillAndSubmitLoginForm($crawler, 'fake@email.com', 'azerty');

        $this->assertResponseRedirects('/login');
        $this->kernelBrowser->followRedirect();
        $this->assertSelectorTextContains('.form-error-message', 'Email could not be found.');
    }

    public function testLoginSuccessful(): void
    {
        $crawler = $this->successfullyLoadLoginPage();

        $this->fillAndSubmitLoginForm($crawler, 'hello@lionel-d.com', 'password');

        $this->assertResponseRedirects('/app/');
        $this->kernelBrowser->followRedirect();
        $this->assertSelectorTextContains('h1', 'Welcome Lionel');
    }

    public function testLoginAsAlreadyAuthenticated(): void
    {
        $this->assertLoggedAsUser();

        $this->kernelBrowser->request('GET', '/login');

        $this->assertResponseRedirects('/app/');
        $this->kernelBrowser->followRedirect();
        $this->assertSelectorTextContains('h1', 'Welcome Lionel');
    }

    public function testLogout(): void
    {
        $this->assertLoggedAsUser();

        $this->kernelBrowser->request('GET', '/logout');

        $this->assertResponseRedirects();
        $this->kernelBrowser->followRedirect();
        $this->assertSelectorTextContains('h1', 'This is Nestor');
    }

    public function testRegisterFailedNoEmail(): void
    {
        $crawler = $this->successfullyLoadRegisterPage();
        $formData = [
            'email' => '',
            'name' => 'NewUser',
            'password' => '1newpa$$',
            'terms' => true,
        ];

        $this->fillAndSubmitRegisterForm($crawler, $formData);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.form-error-message', 'You must enter an email');
    }

    public function testRegisterFailedInvalidEmail(): void
    {
        $crawler = $this->successfullyLoadRegisterPage();
        $formData = [
            'email' => 'Not an email',
            'name' => 'NewUser',
            'password' => '1newpa$$',
            'terms' => true,
        ];

        $this->fillAndSubmitRegisterForm($crawler, $formData);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.form-error-message', 'This is not a valid email');
    }

    public function testRegisterFailedEmailAlreadyUsed(): void
    {
        $crawler = $this->successfullyLoadRegisterPage();
        $formData = [
            'email' => 'hello@lionel-d.com',
            'name' => 'NewUser',
            'password' => '1newpa$$',
            'terms' => true,
        ];

        $this->fillAndSubmitRegisterForm($crawler, $formData);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.form-error-message', 'There is already an account with this email');
    }

    public function testRegisterFailedNoName(): void
    {
        $crawler = $this->successfullyLoadRegisterPage();
        $formData = [
            'email' => 'new@user.com',
            'name' => '',
            'password' => '1newpa$$',
            'terms' => true,
        ];

        $this->fillAndSubmitRegisterForm($crawler, $formData);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.form-error-message', 'Please choose a name');
    }

    public function testRegisterFailedNoPassword(): void
    {
        $crawler = $this->successfullyLoadRegisterPage();
        $formData = [
            'email' => 'new@user.com',
            'name' => 'NewUser',
            'password' => '',
            'terms' => true,
        ];

        $this->fillAndSubmitRegisterForm($crawler, $formData);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.form-error-message', 'Please choose a password');
    }

    public function testRegisterFailedPasswordTooShort(): void
    {
        $crawler = $this->successfullyLoadRegisterPage();
        $formData = [
            'email' => 'new@user.com',
            'name' => 'NewUser',
            'password' => '1a$',
            'terms' => true,
        ];

        $this->fillAndSubmitRegisterForm($crawler, $formData);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.form-error-message', 'Your password should be at least 8 characters long');
    }

    public function testRegisterFailedPasswordWithoutLetter(): void
    {
        $crawler = $this->successfullyLoadRegisterPage();
        $formData = [
            'email' => 'new@user.com',
            'name' => 'NewUser',
            'password' => '$1234567',
            'terms' => true,
        ];

        $this->fillAndSubmitRegisterForm($crawler, $formData);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.form-error-message', 'Your password must contain at least one letter');
    }

    public function testRegisterFailedPasswordWithoutDigit(): void
    {
        $crawler = $this->successfullyLoadRegisterPage();
        $formData = [
            'email' => 'new@user.com',
            'name' => 'NewUser',
            'password' => '$abcdefg',
            'terms' => true,
        ];

        $this->fillAndSubmitRegisterForm($crawler, $formData);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.form-error-message', 'Your password must contain at least one digit');
    }

    public function testRegisterFailedPasswordWithoutSymbol(): void
    {
        $crawler = $this->successfullyLoadRegisterPage();
        $formData = [
            'email' => 'new@user.com',
            'name' => 'NewUser',
            'password' => '1234567x',
            'terms' => true,
        ];

        $this->fillAndSubmitRegisterForm($crawler, $formData);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.form-error-message', 'Your password must contain at least one symbol');
    }

    public function testRegisterFailedTermsNotAgreed(): void
    {
        $crawler = $this->successfullyLoadRegisterPage();
        $formData = [
            'email' => 'new@user.com',
            'name' => 'NewUser',
            'password' => '1newpa$$',
            'terms' => false,
        ];

        $this->fillAndSubmitRegisterForm($crawler, $formData);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.form-error-message', 'You should agree to our terms');
    }

    public function testRegisterSuccessful(): void
    {
        $crawler = $this->successfullyLoadRegisterPage();
        $formData = [
            'email' => 'new@user.com',
            'name' => 'NewUser',
            'password' => '1newpa$$',
            'terms' => true,
        ];

        $this->fillAndSubmitRegisterForm($crawler, $formData);

        $this->assertResponseRedirects('/app/');
        $this->kernelBrowser->followRedirect();
        $this->assertSelectorTextContains('h1', 'Welcome NewUser');
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

    private function successfullyLoadRegisterPage(): Crawler
    {
        $crawler = $this->kernelBrowser->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Please register');

        return $crawler;
    }

    /**
     * @param mixed[] $formData
     */
    private function fillAndSubmitRegisterForm(Crawler $crawler, array $formData): void
    {
        $form = $crawler->selectButton('register_submit')->form();

        $form['registration[email]'] = $formData['email'];
        $form['registration[name]'] = $formData['name'];
        $form['registration[plainPassword]'] = $formData['password'];

        if ($formData['terms']) {
            $form['registration[agreeTerms]'] = '1';
        }

        $this->kernelBrowser->submit($form);
    }
}
