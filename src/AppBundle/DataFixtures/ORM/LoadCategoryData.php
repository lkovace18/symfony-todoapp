<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $workCategory = new Category();
        $workCategory->setName('work');
        $manager->persist($workCategory);
        $this->addReference('work-category', $workCategory);

        $privateCategory = new Category();
        $privateCategory->setName('private');
        $manager->persist($privateCategory);
        $this->addReference('private-category', $privateCategory);

        $funCategory = new Category();
        $funCategory->setName('fun');
        $manager->persist($funCategory);
        $this->addReference('fun-category', $funCategory);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
