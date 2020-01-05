<?php

namespace App\Tests\Helper;

use App\Helper\TransformHelper;
use PHPUnit\Framework\TestCase;

class TransformHelperTest extends TestCase
{

    public function testShout()
    {
        $this->assertEquals('', TransformHelper::shout(''));
        $this->assertEquals('LOREM!', TransformHelper::shout('lorem'));
        $this->assertEquals('LOREM!', TransformHelper::shout('lorem!'));
        $this->assertEquals('LOREM!', TransformHelper::shout('lorem! '));
        $this->assertEquals('LOREM!!', TransformHelper::shout('lorem!!'));
        $this->assertEquals('LOREM!', TransformHelper::shout('lorem. '));
        $this->assertEquals('LOREM IPSUM!', TransformHelper::shout('Lorem Ipsum.'));
        $this->assertEquals('LOREM! IPSUM!', TransformHelper::shout('Lorem! Ipsum.'));
    }

    public function testSlugify()
    {
        $this->assertEquals('lorem-ipsum', TransformHelper::slugify('–Lorem Ipsum'));
        $this->assertEquals('lorem-ipsum', TransformHelper::slugify(' Lorem Ipsum '));
    }
}
