<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CategoryControllerTest extends WebTestCase
{
    private CategoryRepository $categoryRepository;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->categoryRepository = $entityManager->getRepository(Category::class);
    }

    private function testPaginatedResponseFormat(): void
    {
        // Retrieve the result of the response
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);
        // Check the presence and the type of the "data" field
        $this->assertArrayHasKey("data", $result);
        $this->assertIsArray($result["data"]);

        // Check the format of each element within the "data" field
        foreach ($result["data"] as $category) {
            $this->testCategoryFormat($category);
        }

        // Perform the same operations for the "pagination" field
        $this->assertArrayHasKey("pagination", $result);
        $this->assertIsArray($result["pagination"]);

        $paginationKeys = ["total", "count", "offset", "items_per_page", "total_pages", "current_page", "has_next_page", "has_previous_page", ];
        foreach ($paginationKeys as $key) {
            $this->assertArrayHasKey($key, $result["pagination"]);
        }
    }

    /**
     * Test the format of a post element
     */
    private function testCategoryFormat(array $categoryAsArray): void
    {
        // Check the presence of each post fields
        $categoryKeys = ["id", "name"];
        foreach ($categoryKeys as $key) {
            $this->assertArrayHasKey($key, $categoryAsArray);
        }
    }

    /**
     * Test the GET /api/category route
     */
    public function testGetCategories(): void
    {
        // Make a request with default page parameter
        $this->client->request('GET', '/api/category');

        // Check if the request is valid
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame("json");

        // Check the response format
        $this->testPaginatedResponseFormat();

        // Perform the same operations with a custom page parameter
        $this->client->request('GET', '/api/category?page=1');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame("json");

        $this->testPaginatedResponseFormat();
    }

    /**
     * Test the GET /api/category/{id} route
     */
    public function testGetCategory(): void
    {
        // Retrieve a post from the database
        $post = $this->categoryRepository->findOneBy([]);

        // Make the request
        $this->client->request('GET', "/api/category/{$post->getId()}");

        // Check if it's successful
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame("json");

        // Check the response format
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->testCategoryFormat($result);
    }

    /**
     * Test the POST /api/post route
     */
    public function testCreatePost(): void
    {
        $responseAndResult = ["name" => "new Category"];
        $this->client->request(
            'POST',
            "/api/category",
            content: json_encode($responseAndResult)
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->testCategoryFormat($result);
        $this->assertSame($responseAndResult, ["name" => "new Category"]);
    }

    /**
     * Test the PATCH /api/post/{id} route
     */
    public function testPartialUpdate(): void
    {
        $post = $this->categoryRepository->findOneBy([]);
        $this->client->request(
            'PATCH',
            "/api/category/{$post->getId()}",
            content: json_encode(["name" => "test"])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);

        $this->testCategoryFormat($result);
        $this->assertSame("test", $result['name']);
    }

    /**
     * Test the PUT /api/post/{id} route
     */
    public function testFullUpdate(): void
    {
        $category = $this->categoryRepository->findOneBy([]);

        // Valid request
        $this->client->request(
            'PUT',
            "/api/category/{$category->getId()}",
            content: json_encode(["name" => "test"])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->testCategoryFormat($result);

        $this->assertSame("test", $result['name']);
    }
}
