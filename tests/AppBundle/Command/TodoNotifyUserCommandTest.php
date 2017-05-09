<?php

namespace Tests\AppBundle\Command;

use AppBundle\Command\TodoNotifyUserCommand;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class TodoNotifyUserCommandTest extends WebTestCase {

	/** @test */
	public function it_executes_notify_user_command() {
		self::bootKernel();
		$application = new Application(self::$kernel);

		$fixtures = $this->loadFixtures(array(
			'AppBundle\DataFixtures\ORM\LoadUserData',
			'AppBundle\DataFixtures\ORM\LoadTodoData',
			'AppBundle\DataFixtures\ORM\LoadCategoryData',
		))->getReferenceRepository();

		$application->add(new TodoNotifyUserCommand());

		$command = $application->find('todo:notify-user');
		$commandTester = new CommandTester($command);
		$commandTester->execute(array(
			'command' => $command->getName(),
		));

		$output = $commandTester->getDisplay();
		$this->assertContains('Sent: 1', $output);
		$this->assertContains('Todo With due date in next 24h!', $output);

	}
}