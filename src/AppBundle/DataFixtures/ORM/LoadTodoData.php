<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Todo;
use AppBundle\Enum\TodoStatus;
use Carbon\Carbon;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTodoData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $firstTodo = new Todo();
        $firstTodo->setContent('First user work todo!');
        $firstTodo->setUser($this->getReference('first-user'));
        $firstTodo->setCategory($this->getReference('work-category'));
        $firstTodo->setDueDate(Carbon::parse('2017-03-25 17:25:00'));
        $firstTodo->setStatus(TodoStatus::PENDING);
        $manager->persist($firstTodo);
        $this->addReference('first-todo', $firstTodo);

        $secondTodo = new Todo();
        $secondTodo->setContent('Second user Fun todo!');
        $secondTodo->setUser($this->getReference('second-user'));
        $secondTodo->setCategory($this->getReference('fun-category'));
        $secondTodo->setDueDate(Carbon::parse('2017-02-15 11:45:00'));
        $secondTodo->setStatus(TodoStatus::PENDING);
        $manager->persist($secondTodo);
        $this->addReference('second-todo', $secondTodo);

        $thirdTodo = new Todo();
        $thirdTodo->setContent('Todo With due date in next 24h!');
        $thirdTodo->setUser($this->getReference('second-user'));
        $thirdTodo->setCategory($this->getReference('fun-category'));
        $thirdTodo->setDueDate($this->getDateinNext24h());
        $thirdTodo->setStatus(TodoStatus::PENDING);
        $manager->persist($thirdTodo);
        $this->addReference('third-todo', $thirdTodo);

        $fourthTodo = new Todo();
        $fourthTodo->setContent('Sent todo With due date in next 24h!');
        $fourthTodo->setUser($this->getReference('second-user'));
        $fourthTodo->setCategory($this->getReference('fun-category'));
        $fourthTodo->setDueDate($this->getDateinNext24h());
        $fourthTodo->setStatus(TodoStatus::SENT);
        $manager->persist($fourthTodo);
        $this->addReference('fourth-todo', $fourthTodo);

        $manager->flush();
    }

    private function getDateinNext24h()
    {
        return Carbon::now()->addHours(rand(1, 23));
    }

    public function getOrder()
    {
        return 3;
    }
}
