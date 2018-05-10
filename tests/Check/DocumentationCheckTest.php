<?php

namespace SilverStripe\ModuleRatings\Tests\Check;

use PHPUnit\Framework\TestCase;
use SilverStripe\ModuleRatings\Check\DocumentationCheck;
use SilverStripe\ModuleRatings\CheckSuite;
use Symfony\Component\Finder\Finder;

class DocumentationCheckTest extends TestCase
{
    /**
     * @dataProvider runProvider
     * @param string[] $filenames
     * @param bool $expected
     */
    public function testRun($filenames, $expected)
    {
        // Set up the Finder mock
        $finder = $this->getMockBuilder(Finder::class)
            ->setMethods(['directories', 'in', 'name'])
            ->getMock();

        $finder->expects($this->once())->method('directories')->will($this->returnSelf());
        $finder->expects($this->once())->method('in')->will($this->returnSelf());
        $finder->expects($this->once())->method('name')->willReturn($filenames);

        $check = new DocumentationCheck();
        $check->setSuite(new CheckSuite());
        $check->setFinder($finder);
        $check->run();

        $this->assertSame($expected, $check->getSuccessful());
    }

    /**
     * @return array[]
     */
    public function runProvider()
    {
        return [
            'docs folder' => [['doc'], true],
            'doc folder' => [['docs'], true],
        ];
    }
}
