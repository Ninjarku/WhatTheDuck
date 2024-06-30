// tests/unit/mockAuth/MockAuth.php
<?php
class MockAuth {
    public function authenticate($username, $password) {
        // Mock authentication logic
        if ($username === 'testuser' && $password === 'testpassword') {
            return true;
        }
        return false;
    }
}