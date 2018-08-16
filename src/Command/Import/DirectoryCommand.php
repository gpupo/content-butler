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

namespace Gpupo\ContentButler\Command\Import;

use Doctrine\ODM\PHPCR\Document\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class DirectoryCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('butler:import:directory')
            ->setDescription('Put directory files to repository')
            ->addArgument('directory', InputArgument::REQUIRED, 'Source Directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $documentManager = $this->getHelper('phpcr')->getDocumentManager();
        $parentDocument = $documentManager->find(null, '/');

        $argument = $input->getArgument('directory');

        $directory = ('/' === $argument[0]) ? $argument : sprintf('%s/%s', getcwd(), $argument);

        $finder = new Finder();
        $finder->files()->name('*.jpg')->in($directory);

        foreach ($finder as $f) {
            $file = new File();
            $file->setFileContentFromFilesystem($f->getRealPath());
            $nodeName = $this->resolveNodePath($f->getRealPath());
            $output->writeln(sprintf('Node <info>%s</>', $nodeName));

            if ($documentManager->find(null, $nodeName)) {
                $output->writeln(sprintf('Node <warning>%s</> already exists', $nodeName));
            } else {
                $output->writeln(sprintf('Saving node <info>%s</>', $nodeName));
                $file->setNodename($nodeName);
                $file->setParentDocument($parentDocument);
                $documentManager->persist($file);
                $documentManager->flush();
            }
        }
    }

    protected function resolveNodePath($realPath)
    {
        $explode = explode('jcr_root/', $realPath);

        return end($explode);
    }
}
