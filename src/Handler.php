<?php

declare(strict_types=1);

/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace Org_Heigl\PasswordMiddleware;

use Org_Heigl\Password\Password;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Handler implements MiddlewareInterface
{
	private $fields = [];

	public function __construct(string ...$fields)
	{
		$this->fields = $fields;
	}

	public function process(
		ServerRequestInterface $request,
		RequestHandlerInterface $handler
	) : ResponseInterface {
		return $handler->handle($this->harden($request));
	}

	public function __invoke(
		ServerRequestInterface $request,
		ResponseInterface $response,
		callable $next
	): ResponseInterface {
		return $next($this->harden($request), $response);
	}

	private function harden(ServerRequestInterface $request): ServerRequestInterface
	{
		$parsedBody = $request->getParsedBody();
		if (null === $parsedBody) {
			return $request;
		}

		foreach ($this->fields as $field) {

			$parsedBody[$field] = Password::createFromPlainText($parsedBody[$field]);
		}

		return $request->withParsedBody($parsedBody);
	}
}
