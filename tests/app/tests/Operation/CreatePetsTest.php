<?php

declare (strict_types=1);
namespace ApiClients\Tests\Client\PetStore\Operation;

use ApiClients\Client\PetStore\Error as ErrorSchemas;
use ApiClients\Client\PetStore\Hydrator;
use ApiClients\Client\PetStore\Operation;
use ApiClients\Client\PetStore\Operator;
use ApiClients\Client\PetStore\Schema;
use ApiClients\Client\PetStore\WebHook;
use ApiClients\Client\PetStore\Router;
use League\OpenAPIValidation;
use React\Http;
use ApiClients\Contracts;
final class CreatePetsTest extends \WyriHaximus\AsyncTestUtilities\AsyncTestCase
{
    /**
     * @test
     */
    public function call_httpCode_default_requestContentType_application_json_responseContentType_application_json_zero()
    {
        $response = new \React\Http\Message\Response(999, array('Content-Type' => 'application/json'), Schema\Error::SCHEMA_EXAMPLE_DATA);
        $auth = $this->prophesize(\ApiClients\Contracts\HTTP\Headers\AuthenticationInterface::class);
        $auth->authHeader(\Prophecy\Argument::any())->willReturn('Bearer beer')->shouldBeCalled();
        $browser = $this->prophesize(\React\Http\Browser::class);
        $browser->withBase(\Prophecy\Argument::any())->willReturn($browser->reveal());
        $browser->withFollowRedirects(\Prophecy\Argument::any())->willReturn($browser->reveal());
        $browser->request('POST', '/pets', \Prophecy\Argument::type('array'), Schema\CreatePets\Request\ApplicationJson::SCHEMA_EXAMPLE_DATA)->willReturn(\React\Promise\resolve($response))->shouldBeCalled();
        $client = new \ApiClients\Client\PetStore\Client($auth->reveal(), $browser->reveal());
        $result = $client->call(Operation\CreatePets::OPERATION_MATCH, (static function (array $data) : array {
            return $data;
        })(json_decode(Schema\CreatePets\Request\ApplicationJson::SCHEMA_EXAMPLE_DATA, true)));
    }
    /**
     * @test
     */
    public function operations_httpCode_default_requestContentType_application_json_responseContentType_application_json_zero()
    {
        $response = new \React\Http\Message\Response(999, array('Content-Type' => 'application/json'), Schema\Error::SCHEMA_EXAMPLE_DATA);
        $auth = $this->prophesize(\ApiClients\Contracts\HTTP\Headers\AuthenticationInterface::class);
        $auth->authHeader(\Prophecy\Argument::any())->willReturn('Bearer beer')->shouldBeCalled();
        $browser = $this->prophesize(\React\Http\Browser::class);
        $browser->withBase(\Prophecy\Argument::any())->willReturn($browser->reveal());
        $browser->withFollowRedirects(\Prophecy\Argument::any())->willReturn($browser->reveal());
        $browser->request('POST', '/pets', \Prophecy\Argument::type('array'), Schema\CreatePets\Request\ApplicationJson::SCHEMA_EXAMPLE_DATA)->willReturn(\React\Promise\resolve($response))->shouldBeCalled();
        $client = new \ApiClients\Client\PetStore\Client($auth->reveal(), $browser->reveal());
        $result = \React\Async\await($client->operations()->fallback()->createPets(json_decode(Schema\CreatePets\Request\ApplicationJson::SCHEMA_EXAMPLE_DATA, true)));
    }
    /**
     * @test
     */
    public function call_httpCode_201_requestContentType_application_json_empty()
    {
        $response = new \React\Http\Message\Response(201, array());
        $auth = $this->prophesize(\ApiClients\Contracts\HTTP\Headers\AuthenticationInterface::class);
        $auth->authHeader(\Prophecy\Argument::any())->willReturn('Bearer beer')->shouldBeCalled();
        $browser = $this->prophesize(\React\Http\Browser::class);
        $browser->withBase(\Prophecy\Argument::any())->willReturn($browser->reveal());
        $browser->withFollowRedirects(\Prophecy\Argument::any())->willReturn($browser->reveal());
        $browser->request('POST', '/pets', \Prophecy\Argument::type('array'), Schema\CreatePets\Request\ApplicationJson::SCHEMA_EXAMPLE_DATA)->willReturn(\React\Promise\resolve($response))->shouldBeCalled();
        $client = new \ApiClients\Client\PetStore\Client($auth->reveal(), $browser->reveal());
        $result = $client->call(Operation\CreatePets::OPERATION_MATCH, (static function (array $data) : array {
            return $data;
        })(json_decode(Schema\CreatePets\Request\ApplicationJson::SCHEMA_EXAMPLE_DATA, true)));
    }
    /**
     * @test
     */
    public function operations_httpCode_201_requestContentType_application_json_empty()
    {
        $response = new \React\Http\Message\Response(201, array());
        $auth = $this->prophesize(\ApiClients\Contracts\HTTP\Headers\AuthenticationInterface::class);
        $auth->authHeader(\Prophecy\Argument::any())->willReturn('Bearer beer')->shouldBeCalled();
        $browser = $this->prophesize(\React\Http\Browser::class);
        $browser->withBase(\Prophecy\Argument::any())->willReturn($browser->reveal());
        $browser->withFollowRedirects(\Prophecy\Argument::any())->willReturn($browser->reveal());
        $browser->request('POST', '/pets', \Prophecy\Argument::type('array'), Schema\CreatePets\Request\ApplicationJson::SCHEMA_EXAMPLE_DATA)->willReturn(\React\Promise\resolve($response))->shouldBeCalled();
        $client = new \ApiClients\Client\PetStore\Client($auth->reveal(), $browser->reveal());
        $result = \React\Async\await($client->operations()->fallback()->createPets(json_decode(Schema\CreatePets\Request\ApplicationJson::SCHEMA_EXAMPLE_DATA, true)));
        self::assertArrayHasKey('code', $result);
        self::assertSame(201, $result['code']);
    }
}