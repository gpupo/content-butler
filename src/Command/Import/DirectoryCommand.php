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
use Doctrine\ODM\PHPCR\Document\Generic;

class DirectoryCommand extends Command
{
    private $documentManager;

    protected function configure()
    {
        $this
            ->setName('butler:import:directory')
            ->setDescription('Put directory files to repository')
            ->addArgument('directory', InputArgument::REQUIRED, 'Source Directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('directory');
        $directory = ('/' === $argument[0]) ? $argument : sprintf('%s/%s', getcwd(), $argument);
        $finder = new Finder();
        $finder->files()->name('*.jpg')->in($directory);

        $this->documentManager = $this->getHelper('phpcr')->getDocumentManager();

        foreach ($finder as $find) {
            $this->saveFile($find, $output);
        }
    }

    protected function saveFile($find, $output)
    {
        $node = $this->resolveNodePath($find);

        if ($this->documentManager->find(null, $node['full'])) {
            $output->writeln(sprintf('Node <error>%s</> already exists', $node['full']));
        } else {
            $output->writeln(sprintf('Saving node <info>%s</>', $node['full']));

            $file = $this->factoryFile($node);
            $this->documentManager->persist($file);
            $this->documentManager->flush();
        }
    }

    protected function factoryFile(array $node): File
    {
        $parent = $this->resolvParentDocument($node['parent']);
        $file = new File();
        $file->setFileContentFromFilesystem($node['real']);
        $file->setNodename($node['name']);
        $file->setParentDocument($parent);

        return $file;
    }

    protected function createParentDocument($path, $recursive = true): Generic
    {
        $nx = explode('/', $path);
        $list = ['path' => $path];
        $list['name'] = array_pop($nx);
        $list['parent'] = implode('/', $nx);

        $parent = $this->resolvParentDocument($list['parent']);

        $generic = new Generic();
        $generic->setNodename($list['name']);
        $generic->setParentDocument($parent);
        $this->documentManager->persist($generic);
        $this->documentManager->flush();

        return $generic;
    }

    protected function resolvParentDocument($path): Generic
    {
        $parent = $this->documentManager->find(null, $path);

        if(empty($parent)) {
            return $this->createParentDocument($path);
        }

        return $parent;
    }

    protected function resolveNodePath($find): array
    {
        $list=[
            'real'  => $find->getRealPath(),
        ];
        $fx = explode('jcr_root', $find->getRealPath());
        $list['full'] = end($fx);
        $list['relative'] = $find->getRelativePathname();
        $nx = explode('/', $list['full']);
        $list['name'] = array_pop($nx);
        $list['parent'] = implode('/', $nx);

        return $list;
    }
}
