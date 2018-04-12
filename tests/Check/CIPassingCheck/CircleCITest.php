<?php

namespace SilverStripe\ModuleRatings\Tests\Check\CIPassingCheck;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use SilverStripe\ModuleRatings\Check\CIPassingCheck;
use SilverStripe\ModuleRatings\CheckSuite;

class CircleCITest extends TestCase
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
        $this->client->method('get')->willReturn($this->client);

        $this->check = $this->getMock(CIPassingCheck::class, ['checkTravisBuild']);
        $this->check->expects($this->once())->method('checkTravisBuild')->willReturn(false);
        $this->check->setRequestClient($this->client);

        $suite = (new CheckSuite)->setRepositorySlug('foo/bar');
        $this->check->setSuite($suite);
    }

    public function testCircleFetchFailure()
    {
        $this->client->method('getBody')->willReturn(false);
        $this->check->run();
        $this->assertFalse($this->check->getSuccessful());
    }

    public function testCircleNotFoundFailure()
    {
        $this->client->method('getBody')->willReturn('{
            "message": "Project not found"
        }');
        $this->check->run();
        $this->assertFalse($this->check->getSuccessful());
    }

    public function testCircleSuccessful()
    {
        $this->client->method('getBody')->willReturn('[
            {"failed": false},
            {"failed": false},
            {"failed": true}
        ]');
        $this->check->run();
        $this->assertTrue($this->check->getSuccessful());
    }

    public function testCircleUnsuccessful()
    {
        $this->client->method('getBody')->willReturn('{
            {"failed": true},
            {"failed": false},
            {"failed": true}
        }');
        $this->check->run();
        $this->assertFalse($this->check->getSuccessful());
    }

    public function testCircleDefaultReturn()
    {
        $this->client->method('getBody')->willReturn('{
            "unrelated": "information"
        }');
        $this->check->run();
        $this->assertFalse($this->check->getSuccessful());
    }
}
