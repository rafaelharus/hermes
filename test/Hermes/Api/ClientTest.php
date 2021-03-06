<?php

namespace Hermes\Api;

use Cerberus\Cerberus;
use Hermes\Exception\RuntimeException;
use Hermes\Resource\Resource;
use Zend\Cache\StorageFactory;
use Zend\Http\Client as ZendClient;
use Zend\Http\Exception\RuntimeException as ZendHttpRuntimeException;
use Hermes\Exception\NotAvailableException;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-11-10 at 18:27:22.
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $client = $this->getMockBuilder(ZendClient::class)
        ->setMethods(['doRequest'])
        ->setConstructorArgs(['http://127.0.0.1', []])
        ->getMock();
        // @codingStandardsIgnoreStart
        $response = <<<RESP
HTTP/1.1 200 OK
Date:  Fri, 13 Nov 2015 15:43:39 GMT
Server:  Apache/2.2.29 (Unix) PHP/5.6.10 mod_ssl/2.2.29 OpenSSL/0.9.8zc
X-Powered-By:  PHP/5.6.9 ZendServer/8.5.0
X-UA-Compatible:  "IE=edge"
Content-Length:  1256
Keep-Alive:  timeout=5, max=99
Connection:  Keep-Alive
Content-Type:  application/hal+json

{"_links":{"self":{"href":"http:\/\/127.0.0.1\/"},"first":{"href":"http:\/\/127.0.0.1"},"last":{"href":"http:\/\/127.0.0.1\u0026page=1"}},"_embedded":{"items":[{"id":1,"name":"name1"},{"id":2,"name":"name2"}]},"page_count":1,"page_size":25,"total_items":1,"page":1}
RESP;
        // @codingStandardsIgnoreEnd
        $client->method('doRequest')
        ->willReturn($response);

        $this->object = new Client($client, 10);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Hermes\Api\Client::__construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(\Zend\Http\Client::class, $this->object->getZendClient());
        $this->assertSame(10, $this->object->getDepth());
    }

    /**
     * @covers Hermes\Api\Client::setZendClient
     * @covers Hermes\Api\Client::getZendClient
     */
    public function testSetGetZendClient()
    {
        $client = new \Zend\Http\Client('http://127.0.0.1', []);
        $this->object->setZendClient($client);
        $this->assertSame($client, $this->object->getZendClient());
    }

    /**
     * @covers Hermes\Api\Client::setZendClient
     */
    public function testSetZendClientWithoutHost()
    {
        $client = new \Zend\Http\Client();

        $this->setExpectedException(ZendHttpRuntimeException::class);
        $this->object->setZendClient($client);
    }

    /**
     * @covers Hermes\Api\Client::doRequest
     * @covers Hermes\Api\Client::isAvailable
     * @covers Hermes\Api\Client::reportSuccess
     * @covers Hermes\Api\Client::reportFailure
     */
    public function testGetWithException()
    {
        $client = $this->getMockBuilder(ZendClient::class)
        ->setMethods(['doRequest'])
        ->setConstructorArgs(['http://127.0.0.1', []])
        ->getMock();

        $response = <<<RESP
HTTP/1.1 200 OK
Date:  Fri, 13 Nov 2015 15:43:39 GMT
Server:  Apache/2.2.29 (Unix) PHP/5.6.10 mod_ssl/2.2.29 OpenSSL/0.9.8zc
X-Powered-By:  PHP/5.6.9 ZendServer/8.5.0
X-UA-Compatible:  "IE=edge"
Content-Length:  1256
Keep-Alive:  timeout=5, max=99
Connection:  Keep-Alive
Content-Type:  application/text

invalid
RESP;

        $client->method('doRequest')
        ->willReturn($response);

        $this->object->setZendClient($client);

        $this->setExpectedException(RuntimeException::class);
        $response = $this->object->get('/');
    }

    /**
     * @covers Hermes\Api\Client::doRequest
     * @covers Hermes\Api\Client::get
     * @covers Hermes\Api\Client::isAvailable
     * @covers Hermes\Api\Client::reportSuccess
     * @covers Hermes\Api\Client::reportFailure
     */
    public function testGet()
    {
        $response = $this->object->get('/');
        $this->assertInstanceOf(Resource::class, $response);
    }

    /**
     * @covers Hermes\Api\Client::post
     */
    public function testPost()
    {
        $response = $this->object->post('/', []);
        $this->assertInstanceOf(Resource::class, $response);
    }

    /**
     * @covers Hermes\Api\Client::put
     */
    public function testPut()
    {
        $response = $this->object->put('/', []);
        $this->assertInstanceOf(Resource::class, $response);
    }

    /**
     * @covers Hermes\Api\Client::patch
     */
    public function testPatch()
    {
        $response = $this->object->patch('/', []);
        $this->assertInstanceOf(Resource::class, $response);
    }

    /**
     * @covers Hermes\Api\Client::delete
     */
    public function testDelete()
    {
        $response = $this->object->delete('/');
        $this->assertInstanceOf(Resource::class, $response);
    }

    /**
     * @covers Hermes\Api\Client::getDepth
     * @covers Hermes\Api\Client::setDepth
     */
    public function testGetSetDepth()
    {
        $this->object->setDepth(9);
        $this->assertSame(9, $this->object->getDepth());
    }

    /**
     * @covers Hermes\Api\Client::doRequest
     * @covers Hermes\Api\Client::get
     * @covers Hermes\Api\Client::isAvailable
     * @covers Hermes\Api\Client::reportSuccess
     * @covers Hermes\Api\Client::reportFailure
     * @covers Hermes\Api\Client::getCircuitBreaker
     * @covers Hermes\Api\Client::setCircuitBreaker
     */
    public function testGetWithCircuitBreaker()
    {
        $storage = StorageFactory::factory([
            'adapter' => [
                'name' => 'memory',
                'options' => [
                    'namespace' => 'test',
                ],
            ],
            'plugins' => [
                'exception_handler' => [
                    'throw_exceptions' => false,
                ],
            ],
        ]);

        $storage->flush();
        $cerberus = new Cerberus($storage, 2, 2);
        $this->object->setCircuitBreaker($cerberus);
        $this->assertSame($cerberus, $this->object->getCircuitBreaker());

        $response = $this->object->get('/');
        $this->assertInstanceOf(Resource::class, $response);
    }

    /**
     * @covers Hermes\Api\Client::doRequest
     * @covers Hermes\Api\Client::get
     * @covers Hermes\Api\Client::isAvailable
     * @covers Hermes\Api\Client::reportSuccess
     * @covers Hermes\Api\Client::reportFailure
     */
    public function testGetFailureWithCircuitBreaker()
    {
        $storage = StorageFactory::factory([
            'adapter' => [
                'name' => 'memory',
                'options' => [
                    'namespace' => 'test',
                ],
            ],
            'plugins' => [
                'exception_handler' => [
                    'throw_exceptions' => false,
                ],
            ],
        ]);

        $storage->flush();
        $cerberus = new Cerberus($storage, 2, 2);
        $this->object->setCircuitBreaker($cerberus);

        $client = new \Zend\Http\Client('http://127.0.0.1:1', []);
        $this->object->setZendClient($client);

        $this->setExpectedException(RuntimeException::class);
        $this->object->get('/');
    }

    /**
     * @covers Hermes\Api\Client::doRequest
     * @covers Hermes\Api\Client::get
     * @covers Hermes\Api\Client::isAvailable
     * @covers Hermes\Api\Client::reportSuccess
     * @covers Hermes\Api\Client::reportFailure
     */
    public function testGetNotAvailableWithCircuitBreaker()
    {
        $storage = StorageFactory::factory([
            'adapter' => [
                'name' => 'memory',
                'options' => [
                    'namespace' => 'test',
                ],
            ],
            'plugins' => [
                'exception_handler' => [
                    'throw_exceptions' => false,
                ],
            ],
        ]);

        $storage->flush();
        $cerberus = new Cerberus($storage, 2, 2);
        $cerberus->reportFailure();
        $cerberus->reportFailure();
        $cerberus->reportFailure();
        $this->object->setCircuitBreaker($cerberus);

        $client = new \Zend\Http\Client('http://127.0.0.1:1', []);
        $this->object->setZendClient($client);

        $this->setExpectedException(NotAvailableException::class);
        $this->object->get('/');
    }
}
