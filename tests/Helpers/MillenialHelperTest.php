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

use Gpupo\ContentButler\Helpers\MillennialHelper;
use Gpupo\ContentButler\Tests\TestCaseAbstract;

/**
 * @coversNothing
 */
class MillenialHelperTest extends TestCaseAbstract
{
    /**
     * @dataProvider dataProviderNode
     *
     * @param mixed $number
     * @param mixed $expected
     */
    public function testCalculate($number, $expected)
    {
        $helper = new MillennialHelper();
        $string = $helper->calculate($number);

        $this->assertSame($expected, $string);
    }

    public function dataProviderNode()
    {
        return [
            [1, 'a/a'],
            [2, 'a/b'],
            [209, 'a/ha'],
            [999, 'a/al/lk'],
            [1155, 'b/ey'],
            [26000, 'aa/a'],
            [25055, 'z/bc'],
            [26035, 'aa/ai'],
            [50035, 'ay/ai'],
            [50735, 'ay/ab/bg'],
            [999926935, 'bd/ds/ai/iy'],
        ];
    }
}
