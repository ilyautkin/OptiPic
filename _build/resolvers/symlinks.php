<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    $dev = MODX_BASE_PATH . 'Extras/OptiPic/';
    /** @var xPDOCacheManager $cache */
    $cache = $modx->getCacheManager();
    if (file_exists($dev) && $cache) {
        if (!is_link($dev . 'assets/components/optipic')) {
            $cache->deleteTree(
                $dev . 'assets/components/optipic/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_ASSETS_PATH . 'components/optipic/', $dev . 'assets/components/optipic');
        }
        if (!is_link($dev . 'core/components/optipic')) {
            $cache->deleteTree(
                $dev . 'core/components/optipic/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_CORE_PATH . 'components/optipic/', $dev . 'core/components/optipic');
        }
    }
}

return true;