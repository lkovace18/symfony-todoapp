<?php namespace AppBundle\Command;

use AppBundle\Enum\TodoStatus;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TodoNotifyUserCommand extends ContainerAwareCommand {

	use LockableTrait;

	protected function configure() {
		$this
			->setName('todo:notify-user')
			->setDescription('Notify user with due date in next 24h')
			->setHidden(true);
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		if (!$this->lock()) {
			$output->writeln('The command is already running in another process.');

			return 0;
		}

		$em = $this->getContainer()
			->get('doctrine')
			->getManager();

		$repository = $em->getRepository("AppBundle:Todo");

		$todos = $repository->findAllWithDueDateInNext24h();

		$rows = array();
		foreach ($todos as $todo) {

			$this
				->getContainer()
				->get('todo_notify_mailer')
				->sendEmail($todo)
			;

			$todo->setStatus(TodoStatus::SENT);
			$em->persist($todo);

			$rows[] = array(
				$todo->getUser()->getEmail(),
				$todo->getContent(),
				$todo->getStatus(),
			);

		}
		$em->flush();

		$table = new Table($output);
		$table
			->setHeaders(array('Email', 'Content', 'Status'))
			->setRows($rows);
		$table->render();

		$output->writeln('Sent: ' . count($rows));

		$this->release();
	}

}
