<?php
// tests/OptionsResolver/TodoOptionsResolverTest.php

namespace App\Tests\OptionsResolver;

use App\OptionsResolver\PostOptionsResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class PostOptionsResolverTest extends TestCase
{
    private PostOptionsResolver $optionsResolver;

    public function setUp(): void
    {
        $this->optionsResolver = new PostOptionsResolver();
    }

    public function testRequiredTitle()
    {
        $params = [];

        $this->expectException(MissingOptionsException::class);

        $this->optionsResolver
            ->configureTitle(true)
            ->resolve($params);
    }

    public function testValidTitle()
    {
        $params = [
            "title" => "My Title"
        ];

        $result = $this->optionsResolver
            ->configureTitle(true)
            ->resolve($params);

        $this->assertEquals("My Title", $result["title"]);
    }

    public function testInvalidTitle()
    {
        $params = [
            "title" => 3
        ];

        $this->expectException(InvalidOptionsException::class);

        $this->optionsResolver
            ->configureTitle(true)
            ->resolve($params);
    }
}
