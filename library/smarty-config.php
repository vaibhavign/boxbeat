<?php
define("SITE_ROOT", dirname($_SERVER['DOCUMENT_ROOT']));
$include_path = get_include_path().PATH_SEPARATOR;
$include_path .= SITE_ROOT.'/application/library'.PATH_SEPARATOR;
$include_path .= SITE_ROOT.'/application/models'.PATH_SEPARATOR;
$include_path .= APPLICATION_PATH.'/includes'.PATH_SEPARATOR;
set_include_path($include_path);
require_once 'smarty/Smarty.class.php';
$smarty = new Smarty();
$smarty->smarty_dir = SITE_ROOT."/library/smarty/";
$smarty->debugging = false;
$smarty->force_compile = true;
$smarty->caching = false;
$smarty->compile_check = true;
$smarty->cache_lifetime = -1;
$smarty->template_dir = APPLICATION_PATH.'/modules/default/views/scripts/index';
$smarty->compile_dir = APPLICATION_PATH.'/modules/default/views/templates_c';
$smarty->plugins_dir = array($config->smarty_dir.'plugins',
                                            SITE_ROOT."/library/smarty_plugins");

  Zend_Registry::set('smarty', $smarty);

