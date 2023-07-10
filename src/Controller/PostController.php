<?php

namespace App\Controller;

use App\Entity\Post;
use App\OptionsResolver\PaginatorOptionsResolver;
use App\OptionsResolver\PostOptionsResolver;
use App\Repository\PostRepository;
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
class PostController extends AbstractController
{
    public function __construct(PostRepository $postRepository) {
        $this->postRepository = $postRepository;
    }
    #[Route('/post', name: 'post', methods: ["GET"])]
    public function index(Request $request, PaginatorOptionsResolver $paginatorOptionsResolver): JsonResponse
    {
        try {
            $queryParams = $paginatorOptionsResolver
                ->configurePage()
                ->resolve($request->query->all());

            $posts = $this->postRepository->findAllWithPagination($queryParams["page"]);
            return $this->json($posts);
        } catch(\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

    }

    #[Route("/post/{id}", "get_post", methods: ["GET"])]
    public function show($id): JsonResponse
    {
        return $this->json($this->postRepository->find($id));
    }

    #[Route("/post", "create_post", methods: ["POST"])]
    public function store(Request $request, ValidatorInterface $validator, PostOptionsResolver $postOptionsResolver): JsonResponse
    {
        try {
            $requestBody = json_decode($request->getContent(), true);
            $fields = $postOptionsResolver->configureTitle(true)
                ->configureTitle()
                ->configureCategoryId()
                ->configureContent()
                ->resolve($requestBody);

            $post = new Post();
            $post->setTitle($fields["title"]);
            $post->setContent($fields["content"]);
            $post->setPublicationDate(new \DateTime('now'));
            $post->setCategoryId($fields["category_id"]);

            // To validate the entity
            $errors = $validator->validate($post);
            if (count($errors) > 0) {
                throw new InvalidArgumentException((string)$errors);
            }

            $this->postRepository->save($post, true);

            return $this->json($post, status: Response::HTTP_CREATED);

        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    #[Route("/post/{id}", "update_post", methods: ["PATCH", "PUT"])]
    public function update($id, Request $request, PostOptionsResolver $postOptionsResolver, ValidatorInterface $validator, EntityManagerInterface $em): JsonResponse
    {
        try {
            $requestBody = json_decode($request->getContent(), true);

            $fields = $postOptionsResolver->configureTitle(true)
                ->configureTitle()
                ->configureCategoryId()
                ->configureContent()
                ->resolve($requestBody);

            $post = $this->postRepository->find($id);
            $post->setTitle($fields["title"]);
            $post->setContent($fields["content"]);
            $post->setPublicationDate(new \DateTime('now'));
            $post->setCategoryId($fields["category_id"]);

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

    #[Route("/post/{id}", "delete_post", methods: ["DELETE"])]
    public function delete($id)
    {
        $this->postRepository->remove($this->postRepository->find($id), true);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
