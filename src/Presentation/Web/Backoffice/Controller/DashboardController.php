<?php

namespace App\Presentation\Web\Backoffice\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DashboardController
 * @package App\Presentation\Web\Backoffice\Controller
 */
final class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('@BackOffice/dashboard/index.html.twig');
    }
}
