<?php

namespace SilverStripe\ModuleRatings\Tests\Check;

use PHPUnit\Framework\TestCase;
use SilverStripe\ModuleRatings\Check\LicenseCheck;
use SilverStripe\ModuleRatings\CheckSuite;
use Symfony\Component\Finder\Finder;

/**
 * This test covers the same logic used in all file based checks
 */
class LicenseCheckTest extends TestCase
{
    /**
     * @covers \SilverStripe\ModuleRatings\Check\ContributingFileCheck::run
     * @covers \SilverStripe\ModuleRatings\Check\CodeOrSrcFolderCheck::run
     * @covers \SilverStripe\ModuleRatings\Check\DocumentationCheck::run
     * @covers \SilverStripe\ModuleRatings\Check\EditorConfigFileCheck::run
     * @covers \SilverStripe\ModuleRatings\Check\GitAttributesFileCheck::run
     * @covers \SilverStripe\ModuleRatings\Check\LicenseCheck::run
     * @covers \SilverStripe\ModuleRatings\Check\ReadmeCheck::run
     *
     * @param array $filenames
     * @param bool $expected
     * @dataProvider runProvider
     */
    public function testRun($filenames, $expected)
    {
        // Set up the Finder mock
        $finder = $this->getMockBuilder(Finder::class)
            ->setMethods(['files', 'in', 'name'])
            ->getMock();

        $finder->expects($this->once())->method('files')->will($this->returnSelf());
        $finder->expects($this->once())->method('in')->will($this->returnSelf());
        $finder->expects($this->once())->method('name')->willReturn($filenames);

        $check = new LicenseCheck();
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
            'markdown license uppercase' => [['LICENSE.md'], true],
            'markdown license lowercase' => [['license.md'], true],
            'text license uppercase' => [['LICENSE.txt'], true],
            'text license lowercase' => [['license.txt'], true],
            'license uppercase' => [['LICENSE'], true],
            'license lowercase' => [['license'], true],
            'no license' => [[], false],
        ];
    }
}
