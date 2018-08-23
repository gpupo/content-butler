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

namespace Gpupo\ContentButler\Tests\Helper;

use Gpupo\ContentButler\Helper\Node;
use Gpupo\ContentButler\Tests\TestCaseAbstract;
use Doctrine\ODM\PHPCR\Document\Generic;
/**
 * @coversNothing
 */
class NodeTest extends TestCaseAbstract
{
    /**
         * @dataProvider dataProviderPaths
     */
    public function testGetParentDocument($string)
    {
        $helper = new Node($this->getHelperSet()->get('phpcr')->getDocumentManager());
        $path = $helper->resolvParentDocument($string);
        $this->assertInstanceof(Generic::class, $path);
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
