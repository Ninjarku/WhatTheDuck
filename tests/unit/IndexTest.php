<?php

use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    public function testIndex()
    {
        // Start output buffering
        ob_start();
        
        // Include the index.php file
        include 'src/index.php';
        
        // Get the output and end buffering
        $output = ob_get_clean();
        
        // Trim the output to remove any extra whitespace or newlines
        $output = trim($output);
        
        // Assert the trimmed output
        $this->assertEquals('Hello, World!', $output);
    }
}
?>
