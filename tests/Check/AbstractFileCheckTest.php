<?php

namespace SilverStripe\ModuleRatings\Tests\Check;

use PHPUnit\Framework\TestCase;
use SilverStripe\ModuleRatings\Check\ContributingFileCheck;
use SilverStripe\ModuleRatings\Check\EditorConfigFileCheck;
use SilverStripe\ModuleRatings\Check\GitAttributesFileCheck;
use SilverStripe\ModuleRatings\Check\LicenseCheck;
use SilverStripe\ModuleRatings\Check\ReadmeCheck;
use SilverStripe\ModuleRatings\CheckSuite;
use Symfony\Component\Finder\Finder;

/**
 * This test covers the same logic used in all file based checks
 */
class AbstractFileCheckTest extends TestCase
{
    /**
     * @param string $cleckClass
     * @param array $filenames
     * @param bool $expected
     * @dataProvider runProvider
     */
    public function testRun($checkClass, $filenames, $expected)
    {
        // Set up the Finder mock
        $finder = $this->getMockBuilder(Finder::class)
            ->setMethods(['files', 'in', 'name'])
            ->getMock();

        $finder->expects($this->once())->method('files')->will($this->returnSelf());
        $finder->expects($this->once())->method('in')->will($this->returnSelf());
        $finder->expects($this->once())->method('name')->willReturn($filenames);

        $check = new $checkClass();
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
            // License check tests
            'markdown license uppercase' => [LicenseCheck::class, ['LICENSE.md'], true],
            'markdown license lowercase' => [LicenseCheck::class, ['license.md'], true],
            'text license uppercase' => [LicenseCheck::class, ['LICENSE.txt'], true],
            'text license lowercase' => [LicenseCheck::class, ['license.txt'], true],
            'license uppercase' => [LicenseCheck::class, ['LICENSE'], true],
            'license lowercase' => [LicenseCheck::class, ['license'], true],
            'no license' => [LicenseCheck::class, [], false],

            // Contributing check tests
            'markdown contributing mixed case' => [ContributingFileCheck::class, ['CoNtrIbUtIng.md'], true],
            'markdown contributing lowercase' => [ContributingFileCheck::class, ['contributing.md'], true],
            'text contributing uppercase' => [ContributingFileCheck::class, ['CONTRIBUTING.txt'], true],
            'text contributing lowercase' => [ContributingFileCheck::class, ['contributing.txt'], true],
            'contributing uppercase' => [ContributingFileCheck::class, ['CONTRIBUTING'], true],
            'contributing lowercase' => [ContributingFileCheck::class, ['contributing'], true],
            'no contributing' => [ContributingFileCheck::class, [], false],

            // Editorconfig check tests
            'editorconfig' => [EditorConfigFileCheck::class, ['.editorconfig'], true],
            'no editorconfig' => [EditorConfigFileCheck::class, [], false],

            // Gitattributes check tests
            'gitattributes' => [GitAttributesFileCheck::class, ['.gitattributes'], true],
            'no gitattributes' => [GitAttributesFileCheck::class, [], false],

            // Readme check tests
            'markdown readme uppercase' => [ReadmeCheck::class, ['README.md'], true],
            'markdown readme lowercase' => [ReadmeCheck::class, ['readme.md'], true],
            'text readme uppercase' => [ReadmeCheck::class, ['README.txt'], true],
            'text readme lowercase' => [ReadmeCheck::class, ['readme.txt'], true],
            'readme uppercase' => [ReadmeCheck::class, ['README'], true],
            'readme lowercase' => [ReadmeCheck::class, ['readme'], true],
            'no readme' => [ReadmeCheck::class, [], false],
        ];
    }
}
