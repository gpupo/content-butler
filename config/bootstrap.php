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

use Symfony\Component\Dotenv\Dotenv;
use Doctrine\Common\Annotations\AnnotationRegistry;

$autoload = include_once __DIR__.'/../vendor/autoload.php';

if (!class_exists(Dotenv::class)) {
    throw new RuntimeException('Please run "composer require symfony/dotenv" to load the ".env" files configuring the application.');
}
// load all the .env files
(new Dotenv())->loadEnv(dirname(__DIR__).'/.env');

AnnotationRegistry::registerLoader(array($autoload, 'loadClass'));

return $autoload;
