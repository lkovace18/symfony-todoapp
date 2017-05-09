<?php

namespace Tests\AppBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class TodoControllerTest extends WebTestCase {

	/** @test */
	public function loggedin_users_can_see_todos() {
		$fixtures = $this->loadFixtures(array(
			'AppBundle\DataFixtures\ORM\LoadUserData',
		))->getReferenceRepository();

		$this->loginAs($fixtures->getReference('admin-user'), 'main');

		$client = $this->makeClient();

		$crawler = $client->request('GET', 'todo');
		$this->assertStatusCode(301, $client);
		$client->followRedirect();
		$this->assertStatusCode(200, $client);

		$this->assertContains(
			'Todos list',
			$client->getResponse()->getContent()
		);

	}

	/** @test */
	public function loggedin_user_can_see_his_todos() {
		$fixtures = $this->loadFixtures(array(
			'AppBundle\DataFixtures\ORM\LoadUserData',
			'AppBundle\DataFixtures\ORM\LoadTodoData',
			'AppBundle\DataFixtures\ORM\LoadCategoryData',
		))->getReferenceRepository();

		$this->loginAs($fixtures->getReference('first-user'), 'main');

		$client = $this->makeClient();

		$crawler = $client->request('GET', 'todo');
		$this->assertStatusCode(301, $client);
		$client->followRedirect();
		$this->assertStatusCode(200, $client);

		$this->assertContains(
			'First user work todo!',
			$client->getResponse()->getContent()
		);
	}

	/** @test */
	public function user_cannot_see_other_users_todos() {
		$fixtures = $this->loadFixtures(array(
			'AppBundle\DataFixtures\ORM\LoadUserData',
			'AppBundle\DataFixtures\ORM\LoadTodoData',
			'AppBundle\DataFixtures\ORM\LoadCategoryData',
		))->getReferenceRepository();

		$this->loginAs($fixtures->getReference('first-user'), 'main');

		$client = $this->makeClient();

		$crawler = $client->request('GET', 'todo');
		$this->assertStatusCode(301, $client);
		$client->followRedirect();
		$this->assertStatusCode(200, $client);

		$this->assertNotContains(
			'Second user fun todo!',
			$client->getResponse()->getContent()
		);
	}

	/** @test */
	public function user_can_see_todo_details() {
		$fixtures = $this->loadFixtures(array(
			'AppBundle\DataFixtures\ORM\LoadUserData',
			'AppBundle\DataFixtures\ORM\LoadTodoData',
			'AppBundle\DataFixtures\ORM\LoadCategoryData',
		))->getReferenceRepository();

		$this->loginAs($fixtures->getReference('first-user'), 'main');

		$client = $this->makeClient();

		$crawler = $client->request('GET', 'todo/' . $fixtures->getReference('first-todo')->getId());
/*		$this->assertStatusCode(301, $client);
$client->followRedirect();*/
		$this->assertStatusCode(200, $client);

		$this->assertContains(
			$fixtures->getReference('first-todo')->getId(),
			$client->getResponse()->getContent()
		);

		$this->assertContains(
			$fixtures->getReference('first-todo')->getContent(),
			$client->getResponse()->getContent()
		);

		$this->assertContains(
			$fixtures->getReference('first-todo')->getCategory()->getName(),
			$client->getResponse()->getContent()
		);
	}

	/** @test */
	public function user_can_edit_todo() {
		$fixtures = $this->loadFixtures(array(
			'AppBundle\DataFixtures\ORM\LoadUserData',
			'AppBundle\DataFixtures\ORM\LoadTodoData',
			'AppBundle\DataFixtures\ORM\LoadCategoryData',
		))->getReferenceRepository();

		$this->loginAs($fixtures->getReference('first-user'), 'main');

		$client = $this->makeClient();

		$crawler = $client->request('GET', '/todo/' . $fixtures->getReference('first-todo')->getId() . '/edit');
		/*$this->assertStatusCode(301, $client);
        $client->followRedirect();*/
		$this->assertStatusCode(200, $client);

		$form = $crawler->selectButton('Edit')->form();
		$form['appbundle_todo[content]']->setValue('Todo Content changed');
		$crawler = $client->submit($form);

		$this->em = static::$kernel->getContainer()
			->get('doctrine')
			->getManager();

		$newTodo = $this->em
			->getRepository('AppBundle:Todo')
			->findOneById($fixtures->getReference('first-todo')->getId());

		$this->assertNotNull($newTodo);

		$this->assertEquals(
			'Todo Content changed',
			$newTodo->getContent()
		);
	}

	/** @test */
	public function user_can_delete_todo() {
		$fixtures = $this->loadFixtures(array(
			'AppBundle\DataFixtures\ORM\LoadUserData',
			'AppBundle\DataFixtures\ORM\LoadTodoData',
			'AppBundle\DataFixtures\ORM\LoadCategoryData',
		))->getReferenceRepository();

		$this->loginAs($fixtures->getReference('first-user'), 'main');

		$client = $this->makeClient();

		$crawler = $client->request('GET', '/todo/' . $fixtures->getReference('first-todo')->getId());
		/*$this->assertStatusCode(301, $client);
        $client->followRedirect();*/
		$this->assertStatusCode(200, $client);

		$form = $crawler->selectButton('Delete')->form();
		$crawler = $client->submit($form);

		$this->em = static::$kernel->getContainer()
			->get('doctrine')
			->getManager();

		$newTodo = $this->em
			->getRepository('AppBundle:Todo')
			->findOneById($fixtures->getReference('first-todo')->getId());

		$this->assertNull($newTodo);
	}

	/** @test */
	public function user_can_add_new_todo() {
		$fixtures = $this->loadFixtures(array(
			'AppBundle\DataFixtures\ORM\LoadUserData',
			'AppBundle\DataFixtures\ORM\LoadTodoData',
			'AppBundle\DataFixtures\ORM\LoadCategoryData',
		))->getReferenceRepository();

		$this->loginAs($fixtures->getReference('first-user'), 'main');

		$client = $this->makeClient();

		$crawler = $client->request('GET', '/todo/new');
		/*$this->assertStatusCode(301, $client);
		$client->followRedirect();*/
		$this->assertStatusCode(200, $client);

		$form = $crawler->selectButton('Create')->form();
		$form['appbundle_todo[content]']->setValue('New Todo for Fun');
		$form['appbundle_todo[category]']->select($fixtures->getReference('fun-category')->getId());
		$form['appbundle_todo[dueDate][date][day]']->select(18);
		$form['appbundle_todo[dueDate][date][month]']->select(8);
		$form['appbundle_todo[dueDate][date][year]']->select(2017);
		$form['appbundle_todo[dueDate][time][hour]']->select(17);
		$crawler = $client->submit($form);

		$this->em = static::$kernel->getContainer()
			->get('doctrine')
			->getManager();

		$newTodo = $this->em
			->getRepository('AppBundle:Todo')
			->findOneByContent('New Todo for Fun');

		$this->assertNotNull($newTodo);

		$this->assertEquals(
			$fixtures->getReference('first-user')->getId(),
			$newTodo->getUser()->getId()
		);

		$this->assertEquals(
			$fixtures->getReference('fun-category')->getId(),
			$newTodo->getCategory()->getId()
		);
	}

}
