<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ── ROOT → langsung ke dashboard blockchain ──
$routes->get('/', 'Blockchain::index');

// ── BLOCKCHAIN ROUTES ─────────────────────────
$routes->get('blockchain',                          'Blockchain::index');
$routes->post('blockchain/addTransaction',          'Blockchain::addTransaction');
$routes->post('blockchain/mineBlock',               'Blockchain::mineBlock');
$routes->get('blockchain/validateChain',            'Blockchain::validateChain');
$routes->get('blockchain/verifyTransaction/(:num)', 'Blockchain::verifyTransaction/$1');
$routes->get('blockchain/getQueueData',             'Blockchain::getQueueData');
$routes->get('blockchain/getBlockchain',            'Blockchain::getBlockchain');
$routes->get('blockchain/getStats',                 'Blockchain::getStats');
$routes->post('blockchain/tamperBlock',             'Blockchain::tamperBlock');
$routes->post('blockchain/restoreChain',            'Blockchain::restoreChain');
$routes->get('blockchain/getMerkleTree/(:num)',     'Blockchain::getMerkleTree/$1');