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

namespace Gpupo\ContentButler\Helpers;

class MillennialHelper
{
    protected $letters;

    public function __construct()
    {
        $this->letters = range('a', 'z');
    }

    public function calculate($number): string
    {
        $x = $number / 1000;
        $m = floor($x);
        $c = $number - ($m * 1000);

        return sprintf('%s/%s', $this->resolveLetter($m), $this->resolveLetter($c - 1));
    }

    protected function resolveLetter($index): string
    {
        $count = count($this->letters);

        if (0 > $index) {
            $index = 0;
        }

        if ($count > $index) {
            return $this->letters[$index];
        }

        $i = 0;
        do {
            ++$i;
            $index -= $count;
        } while ($count < $index);

        $string = $this->resolveLetter($i - 1).$this->letters[$index];

        if (2 < strlen($string)) {
            $string = sprintf('%s/%s', substr($string, 0, 2), substr($string, -2));
        }

        return $string;
    }
}
