<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	protected $baseUrl = 'http://web-chess.localhost.com';

	/**
	 * Creates the application.
	 *
	 * @return \Illuminate\Foundation\Application
	 */
	public function createApplication()
	{
		$app = require __DIR__.'/../bootstrap/app.php';

		$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

		return $app;
	}

	/**
	 * Get the response created by the last call
	 *
	 * @return Response
	 */
	public function getResponse()
	{
		return $this->response;
	}

}
