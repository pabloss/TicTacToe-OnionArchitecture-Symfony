<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the public 'App\Presentation\Web\Pub\Controller\HomeController' shared autowired service.

$this->services['App\Presentation\Web\Pub\Controller\HomeController'] = $instance = new \App\Presentation\Web\Pub\Controller\HomeController(new \App\Core\Domain\Service\PlayersFactory(), new \App\Core\Domain\Service\TurnControl\PlayerRegistry(), new \App\Core\Domain\Service\TurnControl\ErrorLog(), new \App\Presentation\Web\Pub\History\History(($this->privates['App\Repository\HistoryRepository'] ?? $this->load('getHistoryRepositoryService.php'))));

$instance->setContainer((new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($this->getService, array(
    'doctrine' => array('services', 'doctrine', 'getDoctrineService', false),
    'form.factory' => array('services', 'form.factory', 'getForm_FactoryService.php', true),
    'http_kernel' => array('services', 'http_kernel', 'getHttpKernelService', false),
    'parameter_bag' => array('privates', 'parameter_bag', 'getParameterBagService', false),
    'request_stack' => array('services', 'request_stack', 'getRequestStackService', false),
    'router' => array('services', 'router', 'getRouterService', false),
    'security.authorization_checker' => array('services', 'security.authorization_checker', 'getSecurity_AuthorizationCheckerService', false),
    'security.csrf.token_manager' => array('services', 'security.csrf.token_manager', 'getSecurity_Csrf_TokenManagerService.php', true),
    'security.token_storage' => array('services', 'security.token_storage', 'getSecurity_TokenStorageService', false),
    'serializer' => array('services', 'serializer', 'getSerializerService.php', true),
    'session' => array('services', 'session', 'getSessionService.php', true),
    'twig' => array('services', 'twig', 'getTwigService', false),
)))->withContext('App\\Presentation\\Web\\Pub\\Controller\\HomeController', $this));

return $instance;
