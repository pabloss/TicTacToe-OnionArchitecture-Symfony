<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private 'webpack_encore.entrypoint_lookup' shared service.

return $this->privates['webpack_encore.entrypoint_lookup'] = new \Symfony\WebpackEncoreBundle\Asset\EntrypointLookup(($this->privates['webpack_encore.entrypoint_lookup[_default]'] ?? ($this->privates['webpack_encore.entrypoint_lookup[_default]'] = new \Symfony\WebpackEncoreBundle\Asset\EntrypointLookup('/application/public/build/entrypoints.json'))));
