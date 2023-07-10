<?php
namespace App\Tests\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PostControllerTest extends WebTestCase
{
    private PostRepository $postRepository;
    private KernelBrowser $client;

    /**
     * Initializing attributes
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->postRepository = $entityManager->getRepository(Post::class);
    }

    /**
     * Test the format of a paginated response
     */
    private function testPaginatedResponseFormat(): void
    {
        // Retrieve the result of the response
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);
        // Check the presence and the type of the "data" field
        $this->assertArrayHasKey("data", $result);
        $this->assertIsArray($result["data"]);

        // Check the format of each element within the "data" field
        foreach ($result["data"] as $post) {
            $this->testPostFormat($post);
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
    private function testPostFormat(array $postAsArray): void
    {
        // Check the presence of each post fields
        $postKeys = ["id", "title", "content", "publicationDate","categoryId"];
        foreach ($postKeys as $key) {
            $this->assertArrayHasKey($key, $postAsArray);
        }
    }

    /**
     * Test the GET /api/post route
     */
    public function testGetPosts(): void
    {
        // Make a request with default page parameter
        $this->client->request('GET', '/api/post');

        // Check if the request is valid
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame("json");

        // Check the response format
        $this->testPaginatedResponseFormat();

        // Perform the same operations with a custom page parameter
        $this->client->request('GET', '/api/post?page=2');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame("json");

        $this->testPaginatedResponseFormat();

        // Perform the same operations with an invalid page parameter
//        $this->client->request('GET', '/api/post?page=hello');
//        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
//        $this->client->request('GET', '/api/post?page=-2');
//        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Test the GET /api/post/{id} route
     */
    public function testGetPost(): void
    {
        // Retrieve a post from the database
        $post = $this->postRepository->findOneBy([]);

        // Make the request
        $this->client->request('GET', "/api/post/{$post->getId()}");

        // Check if it's successful
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame("json");

        // Check the response format
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->testPostFormat($result);
    }

    /**
     * Test the POST /api/post route
     */
    public function testCreatePost(): void
    {
        $responseAndResult = ["title" => "new Todo", "content" => "ultra mahalay","category_id" => 1];
        $this->client->request(
            'POST',
            "/api/post",
            content: json_encode($responseAndResult)
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->testPostFormat($result);
        $this->assertSame($responseAndResult, ["title" => "new Todo", "content" => "ultra mahalay","category_id" => 1]);
    }

    /**
     * Test the DELETE /api/post/{id} route
     */
    public function testDeletePost(): void
    {
        // As for the previous method, we first make the request without the token header
        $post = $this->postRepository->findOneBy([]);
        $this->client->request(
            'DELETE',
            "/api/post/{$post->getId()}"
        );

        // Check if the request is successful
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    /**
     * Test the PATCH /api/post/{id} route
     */
    public function testPartialUpdate(): void
    {
        $post = $this->postRepository->findOneBy([]);
        $this->client->request(
            'PATCH',
            "/api/post/{$post->getId()}",
            content: json_encode(["title" => "test","content" => "ultra mhalay","category_id" => 1])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);

        $this->testPostFormat($result);
        $this->assertSame("test", $result['title']);
        $this->assertSame("ultra mhalay", $result['content']);
        $this->assertSame(1, $result['categoryId']);
    }

    /**
     * Test the PUT /api/post/{id} route
     */
    public function testFullUpdate(): void
    {
        $post = $this->postRepository->findOneBy([]);

        // Missing parameter
//        $this->client->request(
//            'PUT',
//            "/api/post/{post->getId()}",
//            content: json_encode(["title" => "test","content" => "ultra mhalay"])
//        );

        //$this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        // Valid request
        $this->client->request(
            'PUT',
            "/api/post/{$post->getId()}",
            content: json_encode(["title" => "test","content" => "ultra mhalay","category_id" => 1])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->testPostFormat($result);

        $this->assertSame("test", $result['title']);
        $this->assertSame("ultra mhalay", $result['content']);
        $this->assertSame(1, $result['categoryId']);
    }
}
