<?php

namespace Tests\FOSUserBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase {

	/** @test */
	public function it_displays_login_page() {
		$client = $this->makeClient();
		$crawler = $client->request('GET', 'login');
		$this->assertStatusCode(200, $client);
		$this->assertContains(
			'Log in',
			$client->getResponse()->getContent()
		);
	}

	/** @test */
	public function it_displays_registration_page() {
		$client = $this->makeClient();
		$crawler = $client->request('GET', 'register');
		$client->followRedirect();
		$this->assertStatusCode(200, $client);
		$this->assertContains(
			'Registration',
			$client->getResponse()->getContent()
		);
	}

	/** @test */
	public function user_can_login() {
		$fixtures = $this->loadFixtures(array(
			'AppBundle\DataFixtures\ORM\LoadUserData',
		))->getReferenceRepository();

		$client = $this->makeClient();
		$crawler = $client->request('GET', 'login');
		$form = $crawler->selectButton('_submit')->form();
		$form['_username']->setValue($fixtures->getReference('first-user')->getUsername());
		$form['_password']->setValue('first_secure_password');
		$crawler = $client->submit($form);
		$client->followRedirect();
		$this->assertStatusCode(200, $client);
		$this->assertContains(
			'Logged in as ' . $fixtures->getReference('first-user')->getUsername(),
			$client->getResponse()->getContent()
		);

	}

	/** @test */
	public function user_cannot_login_with_invalid_credentials() {
		$fixtures = $this->loadFixtures(array(
			'AppBundle\DataFixtures\ORM\LoadUserData',
		))->getReferenceRepository();

		$client = $this->makeClient();
		$crawler = $client->request('GET', 'login');
		$form = $crawler->selectButton('_submit')->form();
		$form['_username']->setValue('Invalid_username');
		$form['_password']->setValue('Invalid_password');
		$crawler = $client->submit($form);
		$client->followRedirect();
		$this->assertStatusCode(200, $client);
		$this->assertContains(
			'Invalid credentials',
			$client->getResponse()->getContent()
		);
	}

	/** @test */
	public function user_can_register() {
		$fixtures = $this->loadFixtures(array(
			'AppBundle\DataFixtures\ORM\LoadUserData',
		))->getReferenceRepository();

		$client = $this->makeClient();
		$crawler = $client->request('GET', '/register');
		$client->followRedirect();
		$crawler = $client->getCrawler();

		$form = $crawler->selectButton('Register User')->form();
		$form['fos_user_registration_form[email]']->setValue('demo-user@example.com');
		$form['fos_user_registration_form[username]']->setValue('demo');
		$form['fos_user_registration_form[plainPassword][second]']->setValue('secure_password');
		$form['fos_user_registration_form[plainPassword][first]']->setValue('secure_password');
		$crawler = $client->submit($form);
		$client->followRedirect();
		$this->assertStatusCode(200, $client);
		$this->assertContains(
			'The user has been created successfully.',
			$client->getResponse()->getContent()
		);
	}
}
