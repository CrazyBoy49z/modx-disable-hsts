<?php
/**
 * DisableHSTS build script
 *
 * @package disablehsts
 * @subpackage build
 */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0); // makes sure our script doesn't timeout

/* define version */
define('PKG_NAME', 'DisableHSTS');
define('PKG_NAMESPACE', 'disablehsts');
define('PKG_VERSION', '0.1');
define('PKG_RELEASE', 'alpha1');

$root = dirname(dirname(__FILE__)) . '/';
$sources= array (
    'root' => $root,
    'build' => $root .'_build/',
    'data' => $root . '_build/data/',
    'source_core' => $root.'core/components/' . PKG_NAMESPACE,
);
unset($root); // save memory

require_once dirname(__FILE__) . '/build.config.php';

require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAMESPACE, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAMESPACE, false, true, '{core_path}components/' . PKG_NAMESPACE . '/');

$plugin = $modx->newObject('modPlugin');
$plugin->set('id', 1);
$plugin->set('name', PKG_NAME);
$plugin->set('static', true);
$plugin->set('static_file', PKG_NAMESPACE . '/elements/plugins/' . PKG_NAMESPACE . '.plugin.php');

/* add plugin events */
$events = include $sources['data'] . 'transport.plugin.events.php';
if (is_array($events) && !empty($events)) {
    $plugin->addMany($events);
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Could not find plugin events!');
}
$modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' Plugin Events.'); flush();
unset($events);

$vehicle = $builder->createVehicle($plugin, array(
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'PluginEvents' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
            xPDOTransport::UNIQUE_KEY => array('pluginid', 'event'),
        ),
    ),
));
$vehicle->resolve('file', array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));
$builder->putVehicle($vehicle);

$builder->pack();

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tend = $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\nPackage Built.\nExecution time: {$totalTime}\n");

session_write_close();
exit();
