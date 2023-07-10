<?php

// tests/Repository/TodoRepositoryTest.php

namespace App\Tests\Repository;

use App\Entity\Post;
use App\Model\Paginator;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PostRepositoryTest extends KernelTestCase
{
    private PostRepository $repository;

    public function setUp(): void
    {
        $em = self::getContainer()->get("doctrine")->getManager();
        $this->repository = $em->getRepository(Post::class);
    }

    public function testFindAllWithPagination(): void
    {
        $result = $this->repository->findAllWithPagination(1);

        $this->assertInstanceOf(Paginator::class, $result);
        $this->assertEquals(1, $result->getCurrentPage());
    }
}
