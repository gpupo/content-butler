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

use Gpupo\ContentButler\Helper\MillennialNode;
use Gpupo\ContentButler\Tests\TestCaseAbstract;

/**
 * @coversNothing
 */
class MillenialNodeTest extends TestCaseAbstract
{
    /**
     * @dataProvider dataProviderNode
     *
     * @param mixed $number
     * @param mixed $expected
     */
    public function testCalculate($number, $expected)
    {
        $helper = new MillennialNode();
        $string = $helper->calculate($number);

        $this->assertSame($expected, $string);
    }

    public function dataProviderNode()
    {
        return [
            [999, 'a/al/ll'],
            [1, 'a/b'],
            [1155, 'b/ez'],
            [25055, 'z/bd'],
            [26035, 'aa/aj'],
            [50035, 'ay/aj'],
            [50735, 'ay/ab/bh'],
            [999926935, 'bd/ds/ai/iz'],
        ];
    }
}
