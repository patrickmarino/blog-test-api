<?php

namespace App\Tests\Repository;

use App\Entity\Category;
use App\Entity\Post;
use App\Model\Paginator;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryRepositoryTest extends KernelTestCase
{
    private CategoryRepository $repository;

    public function setUp(): void
    {
        $em = self::getContainer()->get("doctrine")->getManager();
        $this->repository = $em->getRepository(Category::class);
    }

    public function testFindAllWithPagination(): void
    {
        $result = $this->repository->findAllWithPagination(1);

        $this->assertInstanceOf(Paginator::class, $result);
        $this->assertEquals(1, $result->getCurrentPage());
    }
}
