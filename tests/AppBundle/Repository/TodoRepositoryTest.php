<?php

namespace Tests\AppBundle\Repository;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class TodoRepositoryTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /** @test */
    public function findAllWithDueDateInNext24h()
    {
        $fixtures = $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\LoadUserData',
            'AppBundle\DataFixtures\ORM\LoadTodoData',
            'AppBundle\DataFixtures\ORM\LoadCategoryData',
        ])->getReferenceRepository();

        $dueDateTodos = $this->em
            ->getRepository('AppBundle:Todo')
            ->findAllWithDueDateInNext24h();

        $this->assertCount(1, $dueDateTodos);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}
