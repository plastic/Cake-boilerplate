<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php
 *
 * This is an application wide file to load any function that is not used within a class
 * define. You can also use this to include or require any files in your application.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * App::build(array(
 *     'plugins' => array('/full/path/to/plugins/', '/next/full/path/to/plugins/'),
 *     'models' =>  array('/full/path/to/models/', '/next/full/path/to/models/'),
 *     'views' => array('/full/path/to/views/', '/next/full/path/to/views/'),
 *     'controllers' => array('/full/path/to/controllers/', '/next/full/path/to/controllers/'),
 *     'datasources' => array('/full/path/to/datasources/', '/next/full/path/to/datasources/'),
 *     'behaviors' => array('/full/path/to/behaviors/', '/next/full/path/to/behaviors/'),
 *     'components' => array('/full/path/to/components/', '/next/full/path/to/components/'),
 *     'helpers' => array('/full/path/to/helpers/', '/next/full/path/to/helpers/'),
 *     'vendors' => array('/full/path/to/vendors/', '/next/full/path/to/vendors/'),
 *     'shells' => array('/full/path/to/shells/', '/next/full/path/to/shells/'),
 *     'locales' => array('/full/path/to/locale/', '/next/full/path/to/locale/')
 * ));
 *
 */

/**
 * As of 1.3, additional rules for the inflector are added below
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

/*
 * The model name used for the user role (typically 'Role' or 'Group')
 */
Configure :: write('acl.aro.role.model', 'Role');

/*
 * The model name used for the user (typically 'User')
 */
Configure :: write('acl.aro.user.model', 'User');

/*
 * The name of the database field that can be used to display the role name
 */
Configure :: write('acl.aro.role.display_field', 'name');

/*
 * You can add here role id(s) that are always allowed to access the ACL plugin (by bypassing the ACL check)
 * (This may prevent a user from being rejected from the ACL plugin after a ACL permission update)
 */
Configure :: write('acl.role.access_plugin_role_ids', array());

/*
 * You can add here users id(s) that are always allowed to access the ACL plugin (by bypassing the ACL check)
 * (This may prevent a user from being rejected from the ACL plugin after a ACL permission update)
 */
Configure :: write('acl.role.access_plugin_user_ids', array(1));

/*
 * The users table field used as username in the views
 * It may be a table field or a SQL expression such as "CONCAT(User.lastname, ' ', User.firstname)" for MySQL or "User.lastname||' '||User.firstname" for PostgreSQL
 */
Configure :: write('acl.user.display_name', "username");

/*
 * Add the ACL plugin 'locale' folder to your application locales' folders
 */
App :: build(array('locales' => APP . 'plugins' . DS . 'acl' . DS . 'locale'));

// for plugin fatty
define('FATTY_GIT_PATH', '/usr/local/git/bin/git'); // git path
define('FATTY_GIT_DIR', ROOT . DS . '../.git' . DS); // local .git directory path