<?php
/**
 * AuthComponentTest file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) Tests <https://trac.cakephp.org/wiki/Developement/TestSuite>
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          https://trac.cakephp.org/wiki/Developement/TestSuite CakePHP(tm) Tests
 * @package       cake
 * @subpackage    cake.cake.tests.cases.libs.controller.components
 * @since         CakePHP(tm) v 1.2.0.5347
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
App::uses('PermitComponent', 'Sanction.Controller/Component');
App::uses('Session', 'Controller/Component');
App::uses('Controller', 'Controller');

/**
* TestAuthComponent class
*
* @package       cake
* @subpackage    cake.tests.cases.libs.controller.components
*/
class TestPermitComponent extends PermitComponent {

/**
 * testStop property
 *
 * @var bool false
 * @access public
 */
	var $testStop = false;

/**
 * Sets default login state
 *
 * @var bool true
 * @access protected
 */
	var $_loggedIn = true;

/**
 * stop method
 *
 * @access public
 * @return void
 */
	function _stop() {
		$this->testStop = true;
	}
}

/**
* PermitTestController class
*
* @package       cake
* @subpackage    cake.tests.cases.libs.controller.components
*/
class PermitTestController extends Controller {

/**
 * name property
 *
 * @var string 'AuthTest'
 * @access public
 */
	var $name = 'AuthTest';

/**
 * uses property
 *
 * @var array
 * @access public
 */
	var $uses = array();

/**
 * components property
 *
 * @var array
 * @access public
 */
	var $components = array(
		'Sanction.Permit' => array(
			'path' => 'MockAuthTest',
			'check' => 'id',
			'isTest' => true,
		),
		'Session'
	);

/**
 * testUrl property
 *
 * @var mixed null
 * @access public
 */
	var $testUrl = null;

/**
 * construct method
 *
 * @access private
 * @return void
 */
	function __construct($request, $response) {
		$request->addParams(Router::parse('/permit_test'));
		$request->here = '/permit_test';
		$request->webroot = '/';
		Router::setRequestInfo($request);
		parent::__construct($request, $response);
	}

/**
 * beforeFilter method
 *
 * @access public
 * @return void
 */
	function beforeFilter() {
	}

/**
 * login method
 *
 * @access public
 * @return void
 */
	function login() {
	}

/**
 * admin_login method
 *
 * @access public
 * @return void
 */
	function admin_login() {
	}

/**
 * logout method
 *
 * @access public
 * @return void
 */
	function logout() {
	}

/**
 * add method
 *
 * @access public
 * @return void
 */
	function add() {
		echo "add";
	}

/**
 * add method
 *
 * @access public
 * @return void
 */
	function camelCase() {
		echo "camelCase";
	}

/**
 * redirect method
 *
 * @param mixed $url
 * @param mixed $status
 * @param mixed $exit
 * @access public
 * @return void
 */
	function redirect($url, $status = null, $exit = true) {
		$this->testUrl = Router::url($url);
		return false;
	}


/**
 * Mock delete method
 *
 * @param mixed $url
 * @param mixed $status
 * @param mixed $exit
 * @access public
 * @return void
 */
	function delete($id = null) {
		echo 'delete';
	}
}

/**
* PermitTest class
*
* @package       cake
* @subpackage    cake.tests.cases.libs.controller.components
*/
class PermitTest extends CakeTestCase {

/**
 * name property
 *
 * @var string 'Auth'
 * @access public
 */
	var $name = 'Permit';

/**
 * initialized property
 *
 * @var bool false
 * @access public
 */
	var $initialized = false;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_server = $_SERVER;
		$this->_env = $_ENV;

		Configure::write('Security.salt', 'YJfIxfs2guVoUubWDYhG93b0qyJfIxfs2guwvniR2G0FgaC9mi');
		Configure::write('Security.cipherSeed', 770011223369876);

		$request = new CakeRequest(null, false);
		$request->params = array(
			'pass' => array(),  'named' => array(),
			'plugin' => '', 'controller' => 'Posts',
			'action' => 'index'
		);
		$this->Controller = new PermitTestController($request, $this->getMock('CakeResponse'));

		$collection = new ComponentCollection();
		$collection->init($this->Controller);
		$this->Permit = new TestPermitComponent($collection, array(
			'path' => 'MockAuthTest',
			'check' => 'id',
			'isTest' => true,
		));
		$this->Permit->request = $request;
		$this->Permit->response = $this->getMock('CakeResponse');
		$this->Permit->routes = array();
		$this->Permit->executed = null;
		$this->Controller->Permit = $this->Permit;
		$this->Controller->Components->init($this->Controller);
		$this->Controller->Permit = $this->Permit;

		$this->Controller->Session->delete('Message.auth');
		$this->Controller->Session->write('MockAuthTest',array(
			'User' => array(
				'id' => 'user-logged-in',
				'email' => 'loggedin@domain.com',
				'group' => 'member',
				),
			'Role' => array(
				'id' => 'single-1',
				'name' => 'singleAssociation',
				'description' => 'hasOne or belongsTo association',
				),
			'Group' => array(
				0 => array(
					'id' => 'habtm-1',
					'name' => 'admin',
					'description' => 'HABTM association',
					),
				1 => array(
					'id' => 'habtm-2',
					'name' => 'editors',
					'description' => 'HABTM association',
					),
				),
			)
		);

		$this->initialized = true;
		Router::reload();
		Router::connect('/:controller/:action/*');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		$_SERVER = $this->_server;
		$_ENV = $this->_env;

		$this->Permit->Session->delete('MockAuthTest');
		$this->Permit->Session->delete('Message.auth');
		unset($this->Controller, $this->Permit);
	}

	function testSingleParse() {
		$testRoute = array();
		$this->assertFalse($this->Permit->_parse($testRoute));
	
		$testRoute = array('controller' => 'posts');
		$this->assertTrue($this->Permit->_parse($testRoute));
	
		$testRoute = array('controller' => 'posts', 'action' => 'index');
		$this->assertTrue($this->Permit->_parse($testRoute));
	
		$testRoute = array('plugin' => null, 'controller' => 'posts', 'action' => 'index');
		$this->assertTrue($this->Permit->_parse($testRoute));
	
		$testRoute = array('controller' => 'posts', 'action' => 'add');
		$this->assertFalse($this->Permit->_parse($testRoute));
	
		$testRoute = array('controller' => 'users', 'action' => 'index');
		$this->assertFalse($this->Permit->_parse($testRoute));
	}
	
	function testMultipleParse() {
		$testRoute = array('controller' => 'posts', 'action' => array('index'));
		$this->assertTrue($this->Permit->_parse($testRoute));
	
		$testRoute = array('controller' => 'posts', 'action' => array('index', 'add'));
		$this->assertTrue($this->Permit->_parse($testRoute));
	
		$testRoute = array('controller' => array('posts', 'users'), 'action' => array('index', 'add'));
		$this->assertTrue($this->Permit->_parse($testRoute));
	
		$testRoute = array('plugin' => array(null, 'blog'),
			'controller' => array('posts', 'users'),
			'action' => array('index', 'add')
		);
		$this->assertTrue($this->Permit->_parse($testRoute));
	
		$testRoute = array('controller' => 'posts', 'action' => array('add', 'edit', 'delete'));
		$this->assertFalse($this->Permit->_parse($testRoute));
	}
	
	function testCaseAndInflectionParse() {
		$testRoute = array('controller' => 'POSTS');
		$this->assertTrue($this->Permit->_parse($testRoute));
	
		$this->Controller->params = $this->Permit->_requestParams = array(
			'pass' => array(),  'named' => array(),
			'plugin' => '', 'controller' => 'blog_posts',
			'action' => 'INDEX'
		);
		$testRoute = array('controller' => 'POSTS');
		$this->assertFalse($this->Permit->_parse($testRoute));
	
		$testRoute = array('controller' => 'Blog_POSTS');
		$this->assertTrue($this->Permit->_parse($testRoute));
	
		$testRoute = array('action' => 'inDex');
		$this->assertTrue($this->Permit->_parse($testRoute));
	}
	
	function testDenyAccess() {
		$this->Controller->Permit->settings['path'] = 'MockAuthTest.User';
		$this->Controller->Permit->settings['check'] = 'id';
	
		$testRoute = array('rules' => array());
		$this->assertNull($this->Permit->executed);
		$this->assertFalse($this->Permit->_execute($testRoute));
		$this->Permit->executed = null;
	
		$testRoute = array('rules' => array('deny' => true));
		$this->assertNull($this->Permit->executed);
		$this->assertTrue($this->Permit->_execute($testRoute));
		$this->assertEqual($this->Permit->executed, $testRoute);
	
		$testRoute = array('rules' => array('deny' => false));
		$this->assertFalse($this->Permit->_execute($testRoute));
		$this->assertEqual($this->Permit->executed, $testRoute);
	}
	
	function testAuthenticatedUser() {
		$this->Permit->settings['path'] = 'MockAuthTest.Member';
		$this->Permit->settings['check'] = 'id';
	
		$testRoute = array('rules' => array('auth' => true));
		$this->assertNull($this->Permit->executed);
		$this->assertTrue($this->Permit->_execute($testRoute));
		$this->assertEqual($this->Permit->executed, $testRoute);
	
		$this->Permit->settings['path'] = 'MockAuthTest.User';
		$testRoute = array('rules' => array('auth' => false));
		$this->assertTrue($this->Permit->_execute($testRoute));
		$this->assertEqual($this->Permit->executed, $testRoute);
	}
	
	function testNoUser() {
		$this->Permit->settings['path'] = 'MockAuthTest.Member';
		$this->Permit->settings['check'] = 'id';
	
		$testRoute = array('rules' => array('auth' => array('group' => 'member')));
		$this->assertNull($this->Permit->executed);
		$this->assertTrue($this->Permit->_execute($testRoute));
		$this->assertEqual($this->Permit->executed, $testRoute);
	}

/**
 * Setup a simple, single dim path/check
 * but will not be able to check on associated data
 */
	function testAuthSingleDimensionExecute() {
		$this->Permit->settings['path'] = 'MockAuthTest.User';
		$this->Permit->settings['check'] = 'id';
		$this->Permit->_user = $this->Permit->Session->read($this->Permit->settings['path']);

		# test bool, is logged in
		$testRoute = array('rules' => array('auth' => true));
		$this->assertNull($this->Permit->executed);
		$this->assertFalse($this->Permit->_execute($testRoute));
		$this->assertEqual($this->Permit->executed, $testRoute);

		# test single field matches (false response = authorized)
		$testRoute = array('rules' => array('auth' => array('group' => 'member')));
		$this->assertFalse($this->Permit->_execute($testRoute));
		$this->assertEqual($this->Permit->executed, $testRoute);
		$testRoute = array('rules' => array('auth' => array('group' => 'nonmember')));
		$this->assertTrue($this->Permit->_execute($testRoute));
		$this->assertEqual($this->Permit->executed, $testRoute);

		$testRoute = array('rules' => array('auth' => array('id' => 'user-logged-in')));
		$this->assertFalse($this->Permit->_execute($testRoute));
		$this->assertEqual($this->Permit->executed, $testRoute);
		$testRoute = array('rules' => array('auth' => array('id' => 'user-alt')));
		$this->assertTrue($this->Permit->_execute($testRoute));
		$this->assertEqual($this->Permit->executed, $testRoute);
		$testRoute = array('rules' => array('auth' => array('id' => '*user*')));
		$this->assertTrue($this->Permit->_execute($testRoute));
		$this->assertEqual($this->Permit->executed, $testRoute);
		$testRoute = array('rules' => array('auth' => array('id' => '%user%')));
		$this->assertTrue($this->Permit->_execute($testRoute));
		$this->assertEqual($this->Permit->executed, $testRoute);
	}

/**
 * Setup a full, milti dim path/check
 * WILL be able to check on associated data
 */
	function testAuthMultidimensionalExecute() {
		$this->Controller->Permit->settings['path'] = 'MockAuthTest';
		$this->Controller->Permit->settings['check'] = 'User.id';
		$this->Permit->_user = $this->Permit->Session->read($this->Permit->settings['path']);

		# test bool, is logged in
		$testRoute = array('rules' => array('auth' => true));
		$this->assertNull($this->Controller->Permit->executed);
		$this->assertFalse($this->Controller->Permit->_execute($testRoute));
		$this->assertEqual($this->Controller->Permit->executed, $testRoute);

		# test single field matches (false response = authorized)
		$testRoute = array('rules' => array('auth' => array('/User/group' => 'member')));
		$this->assertFalse($this->Controller->Permit->_execute($testRoute));
		$this->assertEqual($this->Controller->Permit->executed, $testRoute);
		$testRoute = array('rules' => array('auth' => array('/User/group' => 'nonmember')));
		$this->assertTrue($this->Controller->Permit->_execute($testRoute));
		$this->assertEqual($this->Controller->Permit->executed, $testRoute);

		$testRoute = array('rules' => array('auth' => array('/User/id' => 'user-logged-in')));
		$this->assertFalse($this->Controller->Permit->_execute($testRoute));
		$this->assertEqual($this->Controller->Permit->executed, $testRoute);
		$testRoute = array('rules' => array('auth' => array('/User/id' => 'user-alt')));
		$this->assertTrue($this->Controller->Permit->_execute($testRoute));
		$this->assertEqual($this->Controller->Permit->executed, $testRoute);
		$testRoute = array('rules' => array('auth' => array('/User/id' => '*user*')));
		$this->assertTrue($this->Controller->Permit->_execute($testRoute));
		$this->assertEqual($this->Controller->Permit->executed, $testRoute);
		$testRoute = array('rules' => array('auth' => array('/User/id' => '%user%')));
		$this->assertTrue($this->Controller->Permit->_execute($testRoute));
		$this->assertEqual($this->Controller->Permit->executed, $testRoute);

		$testRoute = array('rules' => array('auth' => array('/Role/name' => 'singleAssociation')));
		$this->assertFalse($this->Controller->Permit->_execute($testRoute));
		$this->assertEqual($this->Controller->Permit->executed, $testRoute);
		$testRoute = array('rules' => array('auth' => array('/Role/name' => 'something-else')));
		$this->assertTrue($this->Controller->Permit->_execute($testRoute));
		$this->assertEqual($this->Controller->Permit->executed, $testRoute);

		$testRoute = array('rules' => array('auth' => array('/Group/name' => 'admin')));
		$this->assertFalse($this->Controller->Permit->_execute($testRoute));
		$this->assertEqual($this->Controller->Permit->executed, $testRoute);
		$testRoute = array('rules' => array('auth' => array('/Group/name' => 'editors')));
		$this->assertFalse($this->Controller->Permit->_execute($testRoute));
		$this->assertEqual($this->Controller->Permit->executed, $testRoute);
		$testRoute = array('rules' => array('auth' => array('/Group/description' => 'HABTM association')));
		$this->assertFalse($this->Controller->Permit->_execute($testRoute));
		$this->assertEqual($this->Controller->Permit->executed, $testRoute);
		$testRoute = array('rules' => array('auth' => array('/Group/name' => 'something-else')));
		$this->assertTrue($this->Controller->Permit->_execute($testRoute));
		$this->assertEqual($this->Controller->Permit->executed, $testRoute);

		$testRoute = array('rules' => array('auth' => array('Group.name' => 'admin')));
		$this->assertFalse($this->Controller->Permit->_execute($testRoute));
		$this->assertEqual($this->Controller->Permit->executed, $testRoute);
		$testRoute = array('rules' => array('auth' => array('Group.name' => 'editors')));
		$this->assertFalse($this->Controller->Permit->_execute($testRoute));
		$this->assertEqual($this->Controller->Permit->executed, $testRoute);
		$testRoute = array('rules' => array('auth' => array('Group.description' => 'HABTM association')));
		$this->assertFalse($this->Controller->Permit->_execute($testRoute));
		$this->assertEqual($this->Controller->Permit->executed, $testRoute);
		$testRoute = array('rules' => array('auth' => array('Group.name' => 'something-else')));
		$this->assertTrue($this->Controller->Permit->_execute($testRoute));
		$this->assertEqual($this->Controller->Permit->executed, $testRoute);
	}

	function testStartup() {
		$this->Permit->settings['isTest'] = false;

		$this->Permit->access(
			array('controller' => 'posts', 'action' => array('add', 'edit', 'delete')),
			array('auth' => true),
			array('redirect' => array('controller' => 'users', 'action' => 'login'))
		);
		$this->Permit->startup($this->Controller);
		$this->assertNull($this->Controller->testUrl);

		$this->Permit->access(
			array('controller' => 'users'),
			array('auth' => true),
			array(
				'element' => 'auth_error',
				'redirect' => array('controller' => 'users', 'action' => 'login')
			)
		);
		$this->Permit->startup($this->Controller);
		$this->assertNull($this->Controller->testUrl);

		$this->Permit->access(
			array('controller' => 'posts', 'action' => 'index'),
			array('auth' => true),
			array('redirect' => array('controller' => 'users', 'action' => 'login'))
		);
		$this->Permit->startup($this->Controller);
		$this->assertEqual($this->Controller->testUrl, '/users/login');
		$this->Controller->testUrl = null;

		$this->Controller->params = $this->Permit->_requestParams = array(
			'pass' => array(),  'named' => array(),
			'plugin' => '', 'controller' => 'blog_posts',
			'action' => 'index'
		);
		$this->Permit->startup($this->Controller);
		$this->Permit->access(
			array('controller' => 'blogPosts', 'action' => 'index'),
			array('auth' => true),
			array('redirect' => array('controller' => 'users', 'action' => 'login'))
		);
		$this->Permit->startup($this->Controller);
		$this->assertEqual($this->Controller->testUrl, '/users/login');
	}

	function testAccess() {
		$this->assertEqual(count($this->Permit->routes), 0);
		$this->Permit->access(
			array('controller' => 'posts', 'action' => array('add', 'edit', 'delete')),
			array('auth' => array('group' => 'admin')),
			array('redirect' => array('controller' => 'users', 'action' => 'login'))
		);
		$this->assertEqual(count($this->Permit->routes), 1);

		$this->Permit->access(
			array('controller' => 'users'),
			array('auth' => true),
			array(
				'element' => 'auth_error',
				'redirect' => array('controller' => 'users', 'action' => 'login')
			)
		);
		$this->assertEqual(count($this->Permit->routes), 2);

		$expected = array(
			'route' => array('controller' => 'posts', 'action' => array('add', 'edit', 'delete')),
			'rules' => array('auth' => array('group' => 'admin')),
			'redirect' => array('controller' => 'users', 'action' => 'login'),
			'message' => __('Access denied', true),
			'element' => 'default',
			'params' => array(),
			'key' => 'flash',
		);
		$this->assertEqual(current($this->Permit->routes), $expected);
		reset($this->Permit->routes);

		$expected = array(
			'route' => array('controller' => 'users'),
			'rules' => array('auth' => true),
			'redirect' => array('controller' => 'users', 'action' => 'login'),
			'message' => __('Access denied', true),
			'element' => 'auth_error',
			'params' => array(),
			'key' => 'flash',
		);
		$this->assertEqual(end($this->Permit->routes), $expected);
		reset($this->Permit->routes);
	}

	function testRedirect() {
		$this->Permit->settings['path'] = 'MockAuthTest';
		$this->Permit->settings['check'] = 'User.id';

		# test bool, is logged in
		$testRoute = array(
			'route' => array('controller' => 'posts', 'action' => array('add', 'edit', 'delete')),
			'rules' => array('auth' => array('group' => 'admin')),
			'redirect' => array('controller' => 'users', 'action' => 'login'),
			'message' => __('Access denied', true),
			'element' => 'error',
			'params' => array(),
			'key' => 'flash',
		);

		$this->assertTrue($this->Permit->_execute($testRoute));
		$this->assertEqual($this->Permit->executed, $testRoute);

		$this->Permit->redirect($this->Controller, $testRoute);
		$this->assertEqual($this->Controller->testUrl, '/users/login');

		$session = $this->Controller->Session->read('Message');
		$this->assertEqual($session['flash']['message'], __('Access denied', true));
		$this->assertEqual($session['flash']['element'], 'error');
		$this->assertEqual(count($session['flash']['params']), 0);
	}

	function testReferer() {
		$this->Controller->Session->write('Sanction.referer', array());
		$this->assertEqual('/', $this->Permit->referer());

		$this->Controller->Session->write('Sanction.referer', null);
		$this->assertFalse($this->Permit->referer());

		$this->Controller->Session->write('Sanction.referer', array('controller' => 'users', 'action' => 'login'));
		$this->assertEqual('/users/login', $this->Permit->referer());
	}

	function testPermitObject() {
		Permit::$routes = array();
		Permit::$executed = null;
		$this->assertEqual(count(Permit::$routes), 0);

		Permit::access(
			array('controller' => 'posts', 'action' => array('add', 'edit', 'delete')),
			array('auth' => array('group' => 'admin')),
			array('redirect' => array('controller' => 'users', 'action' => 'login'))
		);
		$this->assertEqual(count(Permit::$routes), 1);

		Permit::access(
			array('controller' => 'users'),
			array('auth' => true),
			array(
				'element' => 'auth_error',
				'redirect' => array('controller' => 'users', 'action' => 'login')
			)
		);
		$this->assertEqual(count(Permit::$routes), 2);

		$expected = array(
			'route' => array('controller' => 'posts', 'action' => array('add', 'edit', 'delete')),
			'rules' => array('auth' => array('group' => 'admin')),
			'redirect' => array('controller' => 'users', 'action' => 'login'),
			'message' => __('Access denied', true),
			'element' => 'default',
			'params' => array(),
			'key' => 'flash',
		);
		$this->assertEqual(current(Permit::$routes), $expected);
		reset(Permit::$routes);

		$expected = array(
			'route' => array('controller' => 'users'),
			'rules' => array('auth' => true),
			'redirect' => array('controller' => 'users', 'action' => 'login'),
			'message' => __('Access denied', true),
			'element' => 'auth_error',
			'params' => array(),
			'key' => 'flash',
		);
		$this->assertEqual(end(Permit::$routes), $expected);
		reset(Permit::$routes);
	}

}