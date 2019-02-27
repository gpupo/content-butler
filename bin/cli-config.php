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

require_once './config/bootstrap.php';

$extraCommands = [];

$server = sprintf('http://%s:%d/server/', getenv('JACKRABBIT_SERVER'), getenv('JACKRABBIT_PORT'));

$params = [
    'jackalope.jackrabbit_uri' => $server,
];

$jackrabbit_workspace = getenv('JACKRABBIT_WORKSPACE');
$jackrabbit_username = getenv('JACKRABBIT_USERNAME');
$jackrabbit_password = getenv('JACKRABBIT_PASSWORD');

// bootstrapping the repository implementation. for jackalope, do this:
$factory = new \Jackalope\RepositoryFactoryJackrabbit();
$repository = $factory->getRepository($params);
$credentials = new \PHPCR\SimpleCredentials($jackrabbit_username, $jackrabbit_password);
$session = $repository->login($credentials, $jackrabbit_workspace);

// prepare the doctrine configuration
$config = new \Doctrine\ODM\PHPCR\Configuration();
$driver = new \Doctrine\ODM\PHPCR\Mapping\Driver\AnnotationDriver(
    new \Doctrine\Common\Annotations\AnnotationReader(),
    'src/Document/',
    'vendor/doctrine/phpcr-odm/lib/Doctrine/ODM/PHPCR/Document'
);
$config->setMetadataDriverImpl($driver);

$dm = \Doctrine\ODM\PHPCR\DocumentManager::create($session, $config);

$helperSet = new \Symfony\Component\Console\Helper\HelperSet([
    'phpcr' => new \PHPCR\Util\Console\Helper\PhpcrHelper($session),
    'phpcr_console_dumper' => new \PHPCR\Util\Console\Helper\PhpcrConsoleDumperHelper(),
    'dm' => new \Doctrine\ODM\PHPCR\Tools\Console\Helper\DocumentManagerHelper(null, $dm),
]);

if (class_exists('Symfony\Component\Console\Helper\QuestionHelper')) {
    $helperSet->set(new \Symfony\Component\Console\Helper\QuestionHelper(), 'question');
} else {
    $helperSet->set(new \Symfony\Component\Console\Helper\DialogHelper(), 'dialog');
}
