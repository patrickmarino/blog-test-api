<?php
namespace App\Tests\Entity;

use App\Entity\Post;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostTest extends KernelTestCase
{
    private EntityManager $em;
    private ValidatorInterface  $validator;

    protected function setUp(): void
    {
        $this->em = self::getContainer()->get('doctrine')->getManager();
        $this->validator = self::getContainer()->get("validator");
    }

    public function testDefaultValues(): void
    {
        $post = new Post();

        // Test default values
        $this->assertNull($post->getId());
        $this->assertNull($post->getTitle());
        $this->assertNull($post->getContent());
        $this->assertNull($post->getPublicationDate());
        $this->assertNull($post->getCategoryId());
    }

    public function testTitle()
    {
        $post = new Post();

        // Test entity constraints
        /** @var ConstraintViolation[] $errors */
        $errors = $this->validator->validateProperty($post, "title");
        $this->assertInstanceOf(NotBlank::class, $errors[0]->getConstraint());

//        $post->setTitle("Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas");
//        /** @var ConstraintViolation[] $errors */
//        $errors = $this->validator->validateProperty($post, "title");
//        $this->assertInstanceOf(Length::class, $errors[0]->getConstraint());

        // Test the title setter and getter methods
        $title = 'Test';
        $post->setTitle($title);
        $this->assertEquals($title, $post->getTitle());
    }

}
