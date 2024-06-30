// tests/mockAuth/LoginTest.php
<?php
use PHPUnit\Framework\TestCase;

require 'mockAuth.php';

class LoginTest extends TestCase {
    private $mockAuth;

    protected function setUp(): void {
        // Initialize the mock authentication class
        $this->mockAuth = new MockAuth();
    }

    public function testSuccessfulLogin() {
        // Simulate form input
        $_POST['cust_username'] = 'testuser';
        $_POST['cust_pass'] = 'testpassword';

        // Simulate the login process
        $result = $this->mockAuth->authenticate($_POST['cust_username'], $_POST['cust_pass']);

        // Assert that the login was successful
        $this->assertTrue($result);
    }

    public function testFailedLogin() {
        // Simulate form input
        $_POST['cust_username'] = 'wronguser';
        $_POST['cust_pass'] = 'wrongpassword';

        // Simulate the login process
        $result = $this->mockAuth->authenticate($_POST['cust_username'], $_POST['cust_pass']);

        // Assert that the login failed
        $this->assertFalse($result);
    }

    public function testFailedUser() {
        // Simulate form input
        $_POST['cust_username'] = 'wronguser';
        $_POST['cust_pass'] = 'testpassword';

        // Simulate the login process
        $result = $this->mockAuth->authenticate($_POST['cust_username'], $_POST['cust_pass']);

        // Assert that the login failed
        $this->assertFalse($result);
    }

    public function testFailedPassword() {
        // Simulate form input
        $_POST['cust_username'] = 'wronguser';
        $_POST['cust_pass'] = 'wrongpassword';

        // Simulate the login process
        $result = $this->mockAuth->authenticate($_POST['cust_username'], $_POST['cust_pass']);

        // Assert that the login failed
        $this->assertFalse($result);
    }
}