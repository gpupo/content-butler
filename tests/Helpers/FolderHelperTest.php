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

use Gpupo\ContentButler\Document\Folder;
use Gpupo\ContentButler\Helpers\FolderHelper;
use Gpupo\ContentButler\Tests\TestCaseAbstract;

/**
 * @coversNothing
 */
class FolderHelperTest extends TestCaseAbstract
{
    /**
     * @dataProvider dataProviderPaths
     *
     * @param mixed $string
     */
    public function testGetParentDocument($string)
    {
        $helper = new FolderHelper($this->getHelperSet()->get('phpcr')->getDocumentManager());
        $path = $helper->resolvParentDocument($string);
        $this->assertInstanceof(Folder::class, $path);
    }

    public function dataProviderPaths()
    {
        return [
            ['fixture'],
            ['tests'],
            ['tests/new'],
            ['tests/new/node'],
            ['tests/new/node2/created'],
            ['tests/new/node3/created/at'],
            ['tests/new/node4/created/at/demand'],
        ];
    }
}
