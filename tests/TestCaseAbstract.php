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

namespace Gpupo\ContentButler\Tests;

use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\HelperSet;

abstract class TestCaseAbstract extends TestCase
{
    private $helperSet;

    public function getHelperSet(): Helperset
    {
        if (empty($this->helperSet)) {
            $path = getcwd().'/vendor/autoload.php';
            $autoload = @include $path;

            AnnotationRegistry::registerLoader([$autoload, 'loadClass']);

            require getcwd().'/bin/cli-config.php';
            $this->helperSet = $helperSet;
        }

        return $this->helperSet;
    }
}
