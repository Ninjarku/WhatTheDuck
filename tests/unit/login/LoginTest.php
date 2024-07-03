// tests/LoginTest.php

<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    protected function setUp(): void
    {
        // Simulate the environment
        if (!isset($_SESSION)) {
            $_SESSION = [];
        }
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [];
    }

    public function testSuccessfulLogin()
    {
        // Simulate valid user input
        $_POST = [
            "cust_username" => "validuser",
            "cust_pass" => "validpass"
        ];

        // Buffer the output
        ob_start();
        require '/var/www/html/process_custlogin.php'; // Adjust this path as needed
        $output = ob_get_clean();
        
        $response = json_decode($output, true);

        // Assert the response
        $this->assertEquals('success', $response['icon']);
        $this->assertEquals('Login successful!', $response['title']);
        $this->assertStringContainsString('Welcome back, validuser', $response['message']);
        $this->assertEquals('admin_index.php', $response['redirect']);
        
        // Assert session variables
        $this->assertEquals('success', $_SESSION['cust_login']);
        $this->assertEquals('validuser', $_SESSION['cust_username']);
        $this->assertEquals(1, $_SESSION['cust_id']); // Assuming user ID is 1
        $this->assertEquals('IT Admin', $_SESSION['cust_rol']);
    }
}
