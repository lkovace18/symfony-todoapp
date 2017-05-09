<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Dashboard controller.
 */
class DashboardController extends Controller {

	/**
	 * Displays application landing page
	 *
	 * @Route("/", name="dashboard")
	 * @Method("GET")
	 *
	 */
	public function indexAction() {
		return $this->render('dashboard/index.html.twig');
	}
}