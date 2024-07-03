<?php
use Symfony\Component\Panther\PantherTestCase;

class LoginTest extends PantherTestCase
{
    public function testLogin()
    {
        $client = static::createPantherClient();

        $crawler = $client->request('GET', 'http://your-app-url/Login.php'); // URL of your login page

        $form = $crawler->selectButton('Sign In')->form([
            'cust_username' => 'your-test-username',
            'cust_pass' => 'your-test-password'
        ]);

        $client->submit($form);

        $this->assertContains('index.php', $client->getCurrentURL()); // Expected redirection after login
    }
}

