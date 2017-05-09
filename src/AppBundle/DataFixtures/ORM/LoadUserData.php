<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface {
	public function load(ObjectManager $manager) {
		$adminUser = new User();
		$adminUser->setUsername('admin_user');
		$adminUser->setPlainPassword('secure_password');
		$adminUser->setEnabled(TRUE);
		$adminUser->setEmail('admin@example.com');

		$manager->persist($adminUser);

		$this->addReference('admin-user', $adminUser);

		$firstUser = new User();
		$firstUser->setUsername('first_user');
		$firstUser->setPlainPassword('first_secure_password');
		$firstUser->setEnabled(TRUE);
		$firstUser->setEmail('first.user@example.com');

		$manager->persist($firstUser);

		$this->addReference('first-user', $firstUser);

		$secondUser = new User();
		$secondUser->setUsername('second_user');
		$secondUser->setPlainPassword('second_secure_password');
		$secondUser->setEnabled(TRUE);
		$secondUser->setEmail('second.user@example.com');

		$manager->persist($secondUser);

		$this->addReference('second-user', $secondUser);

		$manager->flush();
	}

	public function getOrder() {
		return 2;
	}
}