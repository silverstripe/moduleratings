<?php

namespace SilverStripe\ModuleRatings\Tests\Check;

use Exception;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use SilverStripe\ModuleRatings\Check\CIPassingCheck;
use SilverStripe\ModuleRatings\CheckSuite;

class TravisTest extends TestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var CIPassingCheck
     */
    protected $check;

    protected function setUp()
    {
        parent::setUp();

        $this->client = $this->getMockBuilder(Client::class)
            ->setMethods(['get', 'getBody'])
            ->getMock();

        // Mock get to return client, we can then mock the last call easily
        $this->client->method('get')->will($this->returnSelf());

        $this->check = $this->getMockBuilder(CIPassingCheck::class)
            ->setMethods(['checkCircleCiBuild'])
            ->getMock();
        $this->check->setRequestClient($this->client);

        $suite = (new CheckSuite())->setRepositorySlug('foo/bar');
        $this->check->setSuite($suite);
    }

    public function testTravisFetchFailure()
    {
        $this->client->method('getBody')->willReturn(false);
        $this->check->run();
        $this->assertFalse($this->check->getSuccessful());
    }

    public function testTravisNotFoundFailure()
    {
        $this->client->method('getBody')->willReturn('{
            "file": "not found"
        }');
        $this->check->run();
        $this->assertFalse($this->check->getSuccessful());
    }

    public function testTravisSuccessful()
    {
        $this->client->method('getBody')->willReturn('{
            "last_build_result": 0
        }');
        $this->check->run();
        $this->assertTrue($this->check->getSuccessful());
    }

    public function testTravisUnsuccessful()
    {
        $this->client->method('getBody')->willReturn('{
            "last_build_result": 255
        }');
        $this->check->run();
        $this->assertFalse($this->check->getSuccessful());
    }

    public function testTravisDefaultReturn()
    {
        $this->client->method('getBody')->willReturn('{
            "unrelated": "information"
        }');
        $this->check->run();
        $this->assertFalse($this->check->getSuccessful());
    }

    public function testGuzzleThrowsException()
    {
        $this->client->method('getBody')->will($this->throwException(new Exception()));
        $this->check->run();
        $this->assertFalse($this->check->getSuccessful());
    }
}
