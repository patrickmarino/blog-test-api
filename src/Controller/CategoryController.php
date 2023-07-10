<?php

namespace App\Controller;

use App\Entity\Category;
use App\OptionsResolver\CategoryOptionsResolver;
use App\OptionsResolver\PaginatorOptionsResolver;
use App\OptionsResolver\PostOptionsResolver;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/api", "api_", format: "json")]
class CategoryController extends AbstractController
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    #[Route('/category', name: 'category', methods: ["GET"])]
    public function index(Request $request, PaginatorOptionsResolver $paginatorOptionsResolver): JsonResponse
    {
        try {
            $queryParams = $paginatorOptionsResolver
                ->configurePage()
                ->resolve($request->query->all());

            $posts = $this->categoryRepository->findAllWithPagination($queryParams["page"]);
            return $this->json($posts);
        } catch(\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    #[Route("/category/{id}", "get_category", methods: ["GET"])]
    public function show($id): JsonResponse
    {
        return $this->json($this->categoryRepository->find($id));
    }

    #[Route("/category", "create_category", methods: ["POST"])]
    public function store(Request $request, ValidatorInterface $validator, CategoryOptionsResolver $categoryOptionsResolver): JsonResponse
    {
        try {
            $requestBody = json_decode($request->getContent(), true);
            $fields = $categoryOptionsResolver->configureName(true)
                ->configureName()
                ->resolve($requestBody);

            $post = new Category();
            $post->setName($fields["name"]);
            // To validate the entity
            $errors = $validator->validate($post);
            if (count($errors) > 0) {
                throw new InvalidArgumentException((string)$errors);
            }

            $this->categoryRepository->save($post, true);

            return $this->json($post, status: Response::HTTP_CREATED);

        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    #[Route("/category/{id}", "update_category", methods: ["PATCH", "PUT"])]
    public function update($id, Request $request, CategoryOptionsResolver $categoryOptionsResolver, ValidatorInterface $validator, EntityManagerInterface $em): JsonResponse
    {
        try {
            $requestBody = json_decode($request->getContent(), true);

            $fields = $categoryOptionsResolver->configureName(true)
                ->configureName()
                ->resolve($requestBody);

            $post = $this->categoryRepository->find($id);
            $post->setName($fields["name"]);

            $errors = $validator->validate($post);
            if (count($errors) > 0) {
                throw new InvalidArgumentException((string) $errors);
            }

            $em->flush();

            return $this->json($post);
        } catch(\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
