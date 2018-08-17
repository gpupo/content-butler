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
use Symfony\Component\Console\Input\InputOption;
use Gpupo\ContentButler\Document\Document;

class DirectoryCommand extends Command
{
    private $documentManager;

    private $splitter;

    protected function configure()
    {
        $this
            ->setName('butler:import:directory')
            ->setDescription('Put directory files to repository')
            ->addArgument('directory', InputArgument::REQUIRED, 'Source Directory')
            ->addOption(
                'splitter',
                's',
                InputOption::VALUE_REQUIRED,
                'String to split root path',
                'jcr_root'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('directory');
        $directory = ('/' === $argument[0]) ? $argument : sprintf('%s/%s', getcwd(), $argument);
        $finder = new Finder();
        $finder->files()->in($directory)->ignoreVCS(true);
        foreach(explode('|', 'jpg|jpeg|gif|css|png|js|ico|html|xml|txt|pdf|svg|webp|woff') as $ftype) {
            $finder->name(sprintf('*.%s', $ftype));
        }

        $this->documentManager = $this->getHelper('phpcr')->getDocumentManager();
        $this->splitter = $input->getOption('splitter');

        foreach ($finder as $find) {
            try {
                $this->saveFile($find, $output);
            } catch (\Exception $e) {
                $output->writeln(sprintf('<error>%s</>', $e->getMessage()));
            }
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

    protected function factoryFile(array $node): Document
    {
        $parent = $this->resolvParentDocument($node['parent']);
        $file = new Document();
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
        $fx = explode($this->splitter, $find->getRealPath());
        $list['full'] = end($fx);
        $list['relative'] = $find->getRelativePathname();
        $nx = explode('/', $list['full']);
        $list['name'] = array_pop($nx);
        $list['parent'] = implode('/', $nx);

        return $list;
    }
}
