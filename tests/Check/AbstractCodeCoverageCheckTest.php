<?php

namespace SilverStripe\ModuleRatings\Tests\Check;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use SilverStripe\ModuleRatings\Check\AbstractCodeCoverageCheck;
use SilverStripe\ModuleRatings\CheckSuite;

class AbstractCodeCoverageCheckTest extends TestCase
{
    /**
     * @var CheckSuite
     */
    protected $checkSuite;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var AbstractCodeCoverageCheck
     */
    protected $check;

    protected function setUp()
    {
        parent::setUp();

        $this->checkSuite = (new CheckSuite())->setRepositorySlug('foo/bar');

        $this->client = $this->getMockBuilder(Client::class)
            ->setMethods(['get', 'getBody'])
            ->getMock();

        $this->client->method('get')->will($this->returnSelf());

        $this->check = $this->getMockBuilder(AbstractCodeCoverageCheck::class)
            ->setConstructorArgs([$this->client])
            ->getMockForAbstractClass();

        $this->check->setSuite($this->checkSuite);
    }

    public function testGetCoverageReturnsZeroWhenSlugIsNotSet()
    {
        $this->checkSuite->setRepositorySlug(null);
        $this->assertSame(0, $this->check->getCoverage());
    }

    public function testGetCodecovCoverageFetchFailure()
    {
        $this->client->expects($this->once())->method('getBody')->willReturn(false);
        $this->assertFalse($this->check->getCodecovCoverage());
    }

    public function testGetCodecovCoverageNotFoundFailure()
    {
        $this->client->expects($this->once())->method('getBody')->willReturn('{
            "meta": {
                "status": 404
            }
        }');
        $this->assertFalse($this->check->getCodecovCoverage());
    }

    public function testGetCodecovCoverageReturnsResult()
    {
        $this->client->expects($this->once())->method('getBody')->willReturn('{
            "meta": {
                "status": 200
            },
            "commit": {
                "totals": {
                    "c": 85
                }
            }
        }');
        $this->assertSame(85, $this->check->getCodecovCoverage());
    }

    public function testGetCodecovCoverageDefaultReturn()
    {
        $this->client->expects($this->once())->method('getBody')->willReturn('{
            "meta": {
                "status": 200
            },
            "commit": {
                "totals": {
                    "wrong_key": 85
                }
            }
        }');
        $this->assertSame(0, $this->check->getCodecovCoverage());
    }

    public function testGetScrutinizerCoverageFetchFailure()
    {
        $this->client->expects($this->once())->method('getBody')->willReturn(false);
        $this->assertFalse($this->check->getScrutinizerCoverage());
    }

    public function testGetScrutinizerCoverageNotFoundFailure()
    {
        $this->client->expects($this->once())->method('getBody')->willReturn('{
            "applications": []
        }');
        $this->assertFalse($this->check->getScrutinizerCoverage());
    }

    public function testGetScrutinizerCoverageReturnsResult()
    {
        $this->client->expects($this->once())->method('getBody')->willReturn('{
            "applications": {
                "master": {
                    "index": {
                        "_embedded": {
                            "project": {
                                "metric_values": {
                                    "scrutinizer.test_coverage": 0.5
                                }
                            }
                        }
                    }
                }
            }
        }');
        $this->assertEquals(50, $this->check->getScrutinizerCoverage());
    }

    /**
     * This test represents a default Scrutinizer repository API response which will have a code quality
     * rating but not a code coverage rating
     */
    public function testGetScrutinizerCoverageDefaultReturn()
    {
        $this->client->expects($this->once())->method('getBody')->willReturn('{
            "applications": {
                "master": {
                    "index": {
                        "_embedded": {
                            "project": {
                                "metric_values": {
                                    "scrutinizer.quality": 7.423076923076923
                                }
                            }
                        }
                    }
                }
            }
        }');
        $this->assertSame(0, $this->check->getScrutinizerCoverage());
    }
}
