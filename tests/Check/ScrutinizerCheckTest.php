<?php

namespace SilverStripe\ModuleRatings\Tests\Check;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use SilverStripe\ModuleRatings\Check\CIPassingCheck;
use SilverStripe\ModuleRatings\Check\ScrutinizerCheck;
use SilverStripe\ModuleRatings\CheckSuite;

class ScrutinizerCheckTest extends TestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var CIPassingCheck
     */
    protected $check;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->getMockBuilder(Client::class)
            ->setMethods(['get', 'getBody'])
            ->getMock();

        // Mock get to return client, we can then mock the last call easily
        $this->client->method('get')->willReturn($this->client);

        $this->check = new ScrutinizerCheck($this->client);

        $suite = (new CheckSuite())->setRepositorySlug('foo/bar');
        $this->check->setSuite($suite);
    }

    public function testScrutinizerFetchFailure()
    {
        $this->client->method('getBody')->willReturn(false);
        $this->check->run();
        $this->assertFalse($this->check->getSuccessful());
    }

    public function testScrutinizerNotFoundFailure()
    {
        $this->client->method('getBody')->willReturn('{
            "foo": "bar"
        }');
        $this->check->run();
        $this->assertFalse($this->check->getSuccessful());
    }

    /**
     * Check a successful API result is measured against the theshold
     *
     * @param float $quality
     * @param boolean $expected
     * @dataProvider runProvider
     */
    public function testRun($quality, $expected)
    {
        $this->client->method('getBody')->willReturn('{
            "applications": {
                "master": {
                    "index": {
                        "_embedded": {
                            "project": {
                                "metric_values": {
                                    "scrutinizer.quality": ' . $quality . '
                                }
                            }
                        }
                    }
                }
            }
        }');
        $this->check->run();
        $this->assertSame($expected, $this->check->getSuccessful());
    }

    /**
     * @return array[]
     */
    public function runProvider()
    {
        return [
            'lower_than_threshold' => [4.0, false],
            'higher_than_threshold' => [8.5, true],
            'equal_to_threshold' => [ScrutinizerCheck::THRESHOLD, true],
        ];
    }

    public function testScrutinizerUnsuccessful()
    {
        $this->client->method('getBody')->willReturn('{
            {"failed": true},
            {"failed": false},
            {"failed": true}
        }');
        $this->check->run();
        $this->assertFalse($this->check->getSuccessful());
    }

    public function testScrutinizerDefaultReturn()
    {
        $this->client->method('getBody')->willReturn('{
            "unrelated": "information"
        }');
        $this->check->run();
        $this->assertFalse($this->check->getSuccessful());
    }
}
