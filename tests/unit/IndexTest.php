<?php
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    public function testIndex()
    {
        $this->expectOutputString('Hello, World!');
        include 'src/index.php';
    }
}
?>
