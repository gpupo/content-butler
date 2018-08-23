<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/content-butler
 * Created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file
 * LICENSE which is distributed with this source code.
 * Para a informação dos direitos autorais e de licença você deve ler o arquivo
 * LICENSE que é distribuído com este código-fonte.
 * Para obtener la información de los derechos de autor y la licencia debe leer
 * el archivo LICENSE que se distribuye con el código fuente.
 * For more information, see <https://opensource.gpupo.com/>.
 *
 */

namespace Gpupo\ContentButler\Tests\Helpers;

use Gpupo\ContentButler\Document\Document;
use Gpupo\ContentButler\Helpers\FileHelper;
use Gpupo\ContentButler\Tests\TestCaseAbstract;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @coversNothing
 */
class FileHelperTest extends TestCaseAbstract
{
    /**
     * @dataProvider dataProviderPaths
     *
     * @param mixed $string
     */
    public function testFactoryDocument($string, SplFileInfo $expectedFileInfo)
    {
        $helper = new FileHelper($this->getHelperSet()->get('phpcr')->getDocumentManager(), 'Resources');
        $fileInfo = $helper->factoryFileInfo($string);
        $this->assertInstanceof(SplFileInfo::class, $fileInfo);
        $data = $helper->resolveFileData($fileInfo);
        $this->assertSame($expectedFileInfo->getFileName(), $data['name']);
        $document = $helper->factoryDocumentFromFileData($data);
        $this->assertInstanceof(Document::class, $document);
    }

    /**
     * @dataProvider dataProviderPaths
     *
     * @param mixed $string
     */
    public function testSaveDocument($string)
    {
        $helper = new FileHelper($this->getHelperSet()->get('phpcr')->getDocumentManager(), 'Resources');
        $document = $helper->factoryDocument($string);
        $this->assertInstanceof(Document::class, $document);
    }

    public function dataProviderPaths()
    {
        $resources = getcwd().'/Resources/';
        $finder = new Finder();
        $finder->files()->in($resources)->ignoreVCS(true);

        $list = [];

        foreach ($finder as $fileInfo) {
            $list[] = [$fileInfo->getRealPath(), $fileInfo];
        }

        return $list;
    }
}
