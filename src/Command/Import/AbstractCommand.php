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

use Gpupo\ContentButler\Document\Document;
use Gpupo\ContentButler\Helpers\DocumentHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

abstract class AbstractCommand extends Command
{
    protected $documentManager;

    protected $documentHelper;

    protected $iteration = 0;

    protected $overshadow;

    protected function configure()
    {
        $this
            ->addArgument('directory', InputArgument::REQUIRED, 'Source Directory')
            ->addOption(
                'splitter',
                's',
                InputOption::VALUE_REQUIRED,
                'String to split root path',
                'jcr_root'
            )
            ->addOption(
                'versionable',
                'b',
                InputOption::VALUE_REQUIRED,
                'Use versionable Documents',
                false
            )
            ->addOption(
                'overshadow',
                'o',
                InputOption::VALUE_REQUIRED,
                'Overshadow filename',
                false
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('directory');
        $directory = ('/' === $argument[0]) ? $argument : sprintf('%s/%s', getcwd(), $argument);
        $finder = new Finder();
        $finder->files()->in($directory)->ignoreVCS(true);
        foreach (explode('|', 'jpg|jpeg|gif|css|png|js|ico|html|xml|txt|pdf|svg|webp|woff') as $ftype) {
            $finder->name(sprintf('*.%s', $ftype));
        }

        $this->documentManager = $this->getHelper('phpcr')->getDocumentManager();
        $this->overshadow = $input->getOption('overshadow');
        $this->documentHelper = new DocumentHelper($this->documentManager, $input->getOption('splitter'), $input->getOption('versionable'));

        foreach ($finder as $fileInfo) {
            try {
                ++$this->iteration;
                $this->persistDocument($fileInfo, $output);
            } catch (\Exception $e) {
                $output->writeln(sprintf('<error>%s</>', $e->getMessage()));
            }
        }
    }

    protected function persistDocument(SplFileInfo $fileInfo, $output): void
    {
        $document = $this->factoryDocument($fileInfo);

        if ($this->documentManager->find(null, $document->getEndpoint())) {
            throw new \Exception(sprintf('Node %s already exists', $document->getEndpoint()));
        }

        $output->writeln(sprintf('Saving node <info>%s</>', $document->getEndpoint()));
        $this->documentManager->persist($document);
        $this->documentManager->flush();
    }

    protected function factoryDocument($fileInfo): Document
    {
        $document = $this->documentHelper->factoryDocument($fileInfo);

        return $document;
    }
}
