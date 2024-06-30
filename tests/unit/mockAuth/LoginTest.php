// tests/mockAuth/LoginTest.php
use PHPUnit\Framework\TestCase;

require 'tests/unit/mockAuth/MockAuth.php';

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

    // Add more test cases as needed
}