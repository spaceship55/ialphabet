<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Controllers;

use Exception;
use GeminiLabs\SiteReviews\App;
use InvalidArgumentException;
use WP_Error;

abstract class BaseController
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var Database
	 */
	protected $db;

	/**
	 * @var Html
	 */
	protected $html;

	/**
	 * @var Logger
	 */
	protected $log;

	/**
	 * @var Notices
	 */
	protected $notices;

	public function __construct( App $app )
	{
		$this->app     = $app;
		$this->db      = $app->make( 'Database' );
		$this->html    = $app->make( 'Html' );
		$this->log     = $app->make( 'Log\Logger' );
		$this->notices = $app->make( 'Notices' );
	}

	/**
	 * Send a command to its handler and execute it.
	 *
	 * @param object $command
	 *
	 * @return void|null|array|bool
	 */
	public function execute( $command )
	{
		$handlerClass = str_replace( 'Commands', 'Handlers', get_class( $command ));

		if( !class_exists( $handlerClass )) {
			throw new InvalidArgumentException( "Handler {$handlerClass} doesn't exist.");
		}

		$handler = $this->app->make( $handlerClass );

		try {
			return $handler->handle( $command );
		}
		catch( Exception $e ) {
			status_header( 400 );
			$this->notices->addError( new WP_Error( 'reviews_error', $e->getMessage() ));
			$this->log->error( $e->getMessage() );
		}
	}

	/**
	 * Render a view and pass any provided data to the view
	 *
	 * @param string $view
	 *
	 * @return void
	 */
	public function render( $view, array $data = [] )
	{
		$data['db']      = $this->db;
		$data['html']    = $this->html; // singleton
		$data['log']     = $this->log;
		$data['notices'] = $this->notices;

		extract( $data );

		return include $this->app->path . 'views/base.php';
	}

	/**
	 * Validate form data
	 *
	 * @return bool
	 */
	public function validate( array $request, array $rules = [] )
	{
		$session   = $this->app->make( 'Session' ); // singleton
		$validator = $this->app->make( 'Validator' );

		$errors = $validator->validate( $request, $rules );

		if( empty( $errors )) {
			return true;
		}

		$session->set( "{$request['form_id']}-errors", $errors );
		$session->set( "{$request['form_id']}-values", $request );

		return false;
	}
}
