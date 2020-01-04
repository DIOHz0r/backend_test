<?php

namespace App\Tests;

use App\Helper\TransformHelper;
use PHPUnit\Framework\TestCase;

class TransformHelperTest extends TestCase
{
    public function testTransformation()
    {
        $helper = new TransformHelper();
        $this->assertEquals('', $helper(''));
        $this->assertEquals('LOREM!', $helper('lorem'));
        $this->assertEquals('LOREM!', $helper('lorem!'));
        $this->assertEquals('LOREM!', $helper('lorem! '));
        $this->assertEquals('LOREM!!', $helper('lorem!!'));
        $this->assertEquals('LOREM!', $helper('lorem. '));
        $this->assertEquals('LOREM IPSUM!', $helper('Lorem Ipsum.'));
        $this->assertEquals('LOREM! IPSUM!', $helper('Lorem! Ipsum.'));
    }
}
