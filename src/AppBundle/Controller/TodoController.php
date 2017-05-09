<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use AppBundle\Enum\TodoStatus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Todo controller.
 *
 * @Route("todo")
 */
class TodoController extends Controller {
	/**
	 * Lists all todo entities.
	 *
	 * @Route("/", name="todo_index")
	 * @Method("GET")
	 */
	public function indexAction(UserInterface $user) {
		$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Todo');

		return $this->render('todo/index.html.twig', array(
			'todos' => $repository->findByUser($user->getId()),
		));
	}

	/**
	 * Creates a new todo entity.
	 *
	 * @Route("/new", name="todo_new")
	 * @Method({"GET", "POST"})
	 */
	public function newAction(Request $request, UserInterface $user) {
		$todo = new Todo();
		$form = $this->createForm('AppBundle\Form\TodoType', $todo)
			->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$todo->setUser($user);
			$todo->setStatus(TodoStatus::PENDING);
			$em = $this->getDoctrine()->getManager();
			$em->persist($todo);
			$em->flush();

			$this->addFlash('success', 'Todo Added');

			return $this->redirectToRoute('todo_show', array('id' => $todo->getId()));
		}

		return $this->render('todo/new.html.twig', array(
			'todo' => $todo,
			'form' => $form->createView(),
		));
	}

	/**
	 * Finds and displays a todo entity.
	 *
	 * @Route("/{id}", name="todo_show")
	 * @Method("GET")
	 */
	public function showAction(Todo $todo, UserInterface $user) {

		if (!$this->AuthUserIsTodoOwner($todo, $user)) {
			return $this->redirectToRoute('todo_index');
		}

		$deleteForm = $this->createDeleteForm($todo);

		return $this->render('todo/show.html.twig', array(
			'todo' => $todo,
			'delete_form' => $deleteForm->createView(),
		));
	}

	/**
	 * Displays a form to edit an existing todo entity.
	 *
	 * @Route("/{id}/edit", name="todo_edit")
	 * @Method({"GET", "POST"})
	 */
	public function editAction(Request $request, Todo $todo, UserInterface $user) {

		if (!$this->AuthUserIsTodoOwner($todo, $user)) {
			return $this->redirectToRoute('todo_index');
		}

		$deleteForm = $this->createDeleteForm($todo);

		$editForm = $this->createForm('AppBundle\Form\TodoType', $todo)
			->handleRequest($request);

		if ($editForm->isSubmitted() && $editForm->isValid()) {
			$this->getDoctrine()->getManager()->flush();

			$this->addFlash('success', 'Todo Edited');

			return $this->redirectToRoute('todo_edit', array('id' => $todo->getId()));
		}

		return $this->render('todo/edit.html.twig', array(
			'todo' => $todo,
			'edit_form' => $editForm->createView(),
			'delete_form' => $deleteForm->createView(),
		));
	}

	/**
	 * Deletes a todo entity.
	 *
	 * @Route("/{id}", name="todo_delete")
	 * @Method("DELETE")
	 */
	public function deleteAction(Request $request, Todo $todo, UserInterface $user) {

		if (!$this->AuthUserIsTodoOwner($todo, $user)) {
			return $this->redirectToRoute('todo_index');
		}

		$form = $this->createDeleteForm($todo);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($todo);
			$em->flush();

			$this->addFlash('success', 'Todo Deleted');
		}

		return $this->redirectToRoute('todo_index');
	}

	/**
	 * Creates a form to delete a todo entity.
	 *
	 * @param Todo $todo The todo entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm(Todo $todo) {
		return $this->createFormBuilder()
			->setAction($this->generateUrl('todo_delete', array('id' => $todo->getId())))
			->setMethod('DELETE')
			->getForm()
		;
	}

	private function AuthUserIsTodoOwner($todo, $user) {
		if ($todo->getUser()->getId() !== $user->getId()) {
			$this->addFlash(
				'danger',
				'AccessForbiden - not yours todo ;)'
			);
			return false;
		}

		return true;
	}

}
