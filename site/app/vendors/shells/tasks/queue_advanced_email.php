<?php

/**
 * @author rodrigorm@gmail.com
 * @package QueuePlugin
 * @subpackage QueuePlugin.Tasks
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link http://github.com/MSeven/cakephp_queue
 * @see http://bakery.cakephp.org/articles/view/emailcomponent-in-a-cake-shell
 */
App::import('Core', array('Router', 'Controller'));
App::import('Component', 'Email');

class queueAdvancedEmailTask extends Shell {
/**
 * List of default variables for EmailComponent
 *
 * @var array
 */
	public $defaults = array(
		'to' => null,
		'subject' => null,
		'charset' => 'UTF-8',
		'from' => null,
		'sendAs' => 'html',
		'template' => null,
		'debug' => false,
		'additionalParams' => '',
		'layout' => 'default'
	);

	public $timeout = 120;
	public $retries = 0;

/**
 * Controller class
 *
 * @var Controller
 */
	public $Controller;

/**
 * EmailComponent
 *
 * @var EmailComponent
 */
	public $Email;

	public function add() {
		$this->err('Queue Email Task cannot be added via Console.');
		$this->out('Please use createJob() on the QueuedTask Model to create a Proper Email Task.');
		$this->out('The Data Array should look something like this:');
		$this->out(var_export(array(
			'settings' => array(
				'to' => 'email@example.com',
				'subject' => 'Email Subject',
				'from' => 'system@example.com',
				'template' => 'sometemplate'
			),
			'vars' => array(
				'text' => 'hello world'
			)
		), true));
	}

	public function run($settings) {
		include(CONFIGS . 'routes.php');

		$this->Controller = & new Controller();
		if (array_key_exists('controller', $settings)) {
			$this->Controller->_set($settings['controller']);
		}

		$this->Email = & new EmailComponent(null);
		$this->Email->initialize($this->Controller, $this->defaults);
		if (array_key_exists('settings', $settings)) {
			$this->Email->_set(array_filter(am($this->defaults, $settings['settings'])));
			if (array_key_exists('vars', $settings)) {
				foreach ($settings['vars'] as $name => $var) {
					$this->Controller->set($name, $var);
				}
			}
			$result = $this->Email->send();
			if (!$result) {
				$this->failureMessage = $this->Email->smtpError;
			}
			return $result;
		}
		$this->err('Queue Email task called without settings data.');
		return false;
	}
}