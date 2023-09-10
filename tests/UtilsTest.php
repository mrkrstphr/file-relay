<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class UtilsTest extends TestCase
{
    public function testRemoveSemicolon()
    {
        $this->assertEquals('hello world', Utils::cleanTitle('hello: world'));
    }

    public function testRemoveSlashes()
    {
        $this->assertEquals('hello world', Utils::cleanTitle('hello/world'));
        $this->assertEquals('hello world', Utils::cleanTitle('hello\\world'));
    }
}
