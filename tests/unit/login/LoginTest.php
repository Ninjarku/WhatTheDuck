// tests/unit/login/LoginTest.php

<?php
use PHPUnit\Framework\TestCase;

// Include the login script
require '/var/www/html/process_custlogin.php';

class LoginTest extends TestCase
{
    protected $config;
    protected $conn;

    protected function setUp(): void
    {
        $this->config = parse_ini_file('/var/www/private/db-config.ini');
        $this->conn = new mysqli(
            $this->config['host'],
            $this->config['username'],
            $this->config['password'],
            $this->config['dbname']
        );

        // Ensure the database connection is established
        if ($this->conn->connect_error) {
            die('Connection failed: ' . $this->conn->connect_error);
        }
        echo('db success');
    }

    protected function tearDown(): void
    {
        $this->conn->close();
    }

    protected function resetPostData(): void
    {
        $_POST = [];
    }

    public function testSuccessfulLogin()
    {
        $this->resetPostData();
        $_POST['cust_username'] = 'ducktest';
        $_POST['cust_pass'] = '$d!70@G1`O|p';

        ob_start();
        include '/var/www/html/process_custlogin.php';
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals('success', $response['icon']);
        $this->assertEquals('Login successful!', $response['title']);
        $this->assertNotEmpty($response['redirect']);
    }

    public function testInvalidLogin()
    {
        $this->resetPostData();
        $_POST['cust_username'] = 'invalid_user';
        $_POST['cust_pass'] = 'invalid_password';

        ob_start();
        include '/var/www/html/process_custlogin.php';
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals('error', $response['icon']);
        $this->assertEquals('Login failed!', $response['title']);
        $this->assertEquals('Invalid username or password.', $response['message']);
    }

    public function testEmptyFields()
    {
        $this->resetPostData();
        $_POST['cust_username'] = '';
        $_POST['cust_pass'] = '';

        ob_start();
        include '/var/www/html/process_custlogin.php';
        $response = json_decode(ob_get_clean(), true);

        $this->assertEquals('error', $response['icon']);
        $this->assertEquals('Login failed!', $response['title']);
        $this->assertEquals('Please fill in all required fields.', $response['message']);
    }
}
