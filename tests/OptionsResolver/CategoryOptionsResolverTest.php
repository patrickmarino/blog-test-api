<?php
namespace App\Tests\OptionsResolver;

use App\OptionsResolver\CategoryOptionsResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class CategoryOptionsResolverTest extends TestCase
{
    private CategoryOptionsResolver $optionsResolver;

    public function setUp(): void
    {
        $this->optionsResolver = new CategoryOptionsResolver();
    }

    public function testRequiredTitle()
    {
        $params = [];

        $this->expectException(MissingOptionsException::class);

        $this->optionsResolver
            ->configureName(true)
            ->resolve($params);
    }

    public function testValidTitle()
    {
        $params = [
            "name" => "My Title"
        ];

        $result = $this->optionsResolver
            ->configureName(true)
            ->resolve($params);

        $this->assertEquals("My Title", $result["name"]);
    }
}
