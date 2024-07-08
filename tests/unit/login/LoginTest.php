<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    protected function setUp(): void
    {
        ob_start();  // Start output buffering to prevent headers from being sent early
        if (!isset($_SESSION)) {
            $_SESSION = [];
        }
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [];
    }

    protected function tearDown(): void
    {
        ob_end_clean();  // Clean up output buffering
    }

    /**
     * @runInSeparateProcess
     */
    public function testSuccessfulLogin()
    {
        $testconfig = parse_ini_file('/var/www/private/test-config.ini');

        // Simulate valid user input
        $_POST = [
            "cust_username" => $testconfig['testUser'],
            "cust_pass" => $testconfig['testPass']
        ];

        // Include the login processing script
        include '/var/www/html/process_custlogin.php';
        
        // Capture and decode the JSON output from the script
        $output = ob_get_contents();
        $response = json_decode($output, true);

        // Assert the response
        $this->assertEquals('success', $response['icon']);
        $this->assertEquals('Login successful!', $response['title']);
        $this->assertEquals('index.php', $response['redirect']);   
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoginWithBlankFields()
    {
        // Simulate empty input
        $_POST = [
            "cust_username" => "",
            "cust_pass" => ""
        ];

        // Include the login processing script
        include '/var/www/html/process_custlogin.php';

        // Capture and decode the JSON output from the script
        $output = ob_get_contents();
        $response = json_decode($output, true);

        // Assert the response
        $this->assertEquals('error', $response['icon']);
        $this->assertEquals('Login failed!', $response['title']);
        $this->assertEquals('Please fill in all required fields.', $response['message']);
        $this->assertNull($response['redirect']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testLoginWithIncorrectCredentials()
    {
        // Simulate incorrect user input
        $_POST = [
            "cust_username" => "wronguser",
            "cust_pass" => "wrongpass"
        ];

        // Include the login processing script
        include '/var/www/html/process_custlogin.php';

        // Capture and decode the JSON output from the script
        $output = ob_get_contents();
        $response = json_decode($output, true);

        // Assert the response
        $this->assertEquals('error', $response['icon']);
        $this->assertEquals('Login failed!', $response['title']);
        $this->assertEquals('Invalid username or password.', $response['message']);
        $this->assertNull($response['redirect']);
    }
}
?>
