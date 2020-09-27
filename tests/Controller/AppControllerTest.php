<?php

namespace App\Tests\Controller;

use App\Tests\AppTestCase;

final class AppControllerTest extends AppTestCase
{
    public function testIndex(): void
    {
        $this->assertLoggedAsUser();

        $this->kernelBrowser->request('GET', '/app/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Welcome Lionel');
    }
}
