<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
    }

    public function testEmail(): void
    {
        $this->user->setEmail('hello@lionel-d.com');

        $this->assertTrue('hello@lionel-d.com' === $this->user->getEmail());
    }

    public function testUsername(): void
    {
        $this->user->setEmail('hello@lionel-d.com');

        $this->assertTrue('hello@lionel-d.com' === $this->user->getUsername());
    }

    public function testRoles(): void
    {
        $this->assertTrue($this->user->getRoles() === ['ROLE_USER']);

        $this->user->setRoles(['ROLE_ADMIN']);

        $this->assertTrue($this->user->getRoles() === ['ROLE_ADMIN', 'ROLE_USER']);
    }

    public function testPassword(): void
    {
        $this->user->setPassword('Hashed&SaltedPassword');

        $this->assertTrue('Hashed&SaltedPassword' === $this->user->getPassword());
    }

    public function testName(): void
    {
        $this->user->setName('Lionel');

        $this->assertTrue('Lionel' === $this->user->getName());
    }
}
