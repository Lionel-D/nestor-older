<?php

namespace App\Tests\Controller\App;

use App\Tests\AppTestCase;

final class DashboardControllerTest extends AppTestCase
{
    public function testIndex(): void
    {
        $this->assertLoggedAsUser();

        $this->kernelBrowser->request('GET', '/app/dashboard');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Welcome Lionel');
    }
}
