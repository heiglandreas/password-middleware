<?php
/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace Org_Heigl\PasswordMiddlewareTest;

use Laminas\Diactoros\Request;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Org_Heigl\Password\Password;
use Org_Heigl\PasswordMiddleware\Handler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HandlerTest extends TestCase
{
	/**
	 * @covers \Org_Heigl\PasswordMiddleware\Handler::__invoke
	 * @covers \Org_Heigl\PasswordMiddleware\Handler::harden
	 */
	public function testThatPasswordsAreConvertedViaPsr7(): void
	{
		$request = $this->getRequest(['password' => 'test123']);
		$handler = new Handler('password');

		$handler(
			$request,
			new Response(),
			function($request, $response) {
				self::assertInstanceOf(
					Password::class,
					$request->getParsedBody()['password']
				);
				self::assertNotEquals(
					'test123',
					$request->getParsedBody()['password']
				);

				return $response;
			}
		);
	}

	private function getRequest(array $data): ServerRequestInterface
	{
		$factory = new ServerRequestFactory();
		$request = $factory->createServerRequest('POST', '/', []);

		return $request->withParsedBody($data);
	}
}
