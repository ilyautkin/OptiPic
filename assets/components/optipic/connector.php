<?php
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
} else {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var OptiPic $OptiPic */
$OptiPic = $modx->getService('OptiPic', 'OptiPic', MODX_CORE_PATH . 'components/optipic/model/');
$modx->lexicon->load('optipic:default');

// handle request
$corePath = $modx->getOption('optipic_core_path', null, $modx->getOption('core_path') . 'components/optipic/');
$path = $modx->getOption('processorsPath', $OptiPic->config, $corePath . 'processors/');
$modx->getRequest();

/** @var modConnectorRequest $request */
$request = $modx->request;
$request->handleRequest([
    'processors_path' => $path,
    'location' => '',
]);