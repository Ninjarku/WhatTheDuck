<?php
use PHPUnit\Framework\TestCase;

class SignupTest extends TestCase
{
    private $conn = null;

    protected function setUp(): void
    {
        ob_start();
        if (!isset($_SESSION)) {
            $_SESSION = [];
        }
    
        // Load database configuration
        $this->config = parse_ini_file('/var/www/private/db-config.ini');
        // Establish a database connection
        $this->conn = new mysqli(
            $this->config['host'],
            $this->config['username'],
            $this->config['password'],
            $this->config['dbname']
        );
    
        // Start transaction
        if ($this->conn->connect_error) {
            throw new Exception("Connection failed: " . $this->conn->connect_error);
        }

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [];
    }
    
    protected function tearDown(): void
    {
        if ($this->conn) {
            $this->conn->query("DELETE FROM User WHERE Email = 'user@example.com'");
            $this->conn->close();
        }
    
        session_destroy();
        ob_end_clean();
    }
    /**
     * @runInSeparateProcess
     */
    public function testSuccessfulSignup()
    {
        $_POST = [
            "signup_mobile_number" => "12345678",
            "signup_email" => "user@example.com",
            "signup_birthday" => "1990-01-01",
            "signup_username" => "newuser",
            "signup_pwd" => "password123",
            "signup_pwdconfirm" => "password123",
            "agree" => "on"
        ];

        include '/var/www/html/process_custsignup.php';

        $output = ob_get_contents();
        $response = json_decode($output, true);

        $this->assertEquals('success', $response['icon']);
        $this->assertEquals('Signup successful!', $response['title']);
        $this->assertStringContainsString('Click the button to login', $response['message']);
        $this->assertEquals('Login.php', $response['redirect']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSignupWithMissingFields()
    {
        $_POST = [
            "signup_mobile_number" => "",
            "signup_email" => "",
            "signup_birthday" => "",
            "signup_username" => "",
            "signup_pwd" => "",
            "signup_pwdconfirm" => "",
            "agree" => ""
        ];

        include '/var/www/html/process_custsignup.php';

        $output = ob_get_contents();
        $response = json_decode($output, true);

        $this->assertEquals('error', $response['icon']);
        $this->assertEquals('Signup failed!', $response['title']);
        $this->assertEquals('Please fill in all required fields.', $response['message']);
        $this->assertNull($response['redirect']);
    }

}
?>
