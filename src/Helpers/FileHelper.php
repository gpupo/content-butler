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

use Doctrine\ODM\PHPCR\DocumentManager;
use Gpupo\ContentButler\Document\Document;
use Gpupo\ContentButler\Document\DocumentVersionable;
use SplFileInfo;
use Symfony\Component\Finder\Finder;


class FileHelper
{
    protected $documentManager;

    protected $nodeHelper;

    protected $splitter;

    protected $versionable;

    public function __construct(DocumentManager $documentManager, string $splitter = './', $versionable = false)
    {
        $this->documentManager = $documentManager;
        $this->nodeHelper = new FolderHelper($this->documentManager);
        $this->splitter = $splitter;
        $this->versionable = $versionable;
    }

    public function factoryDocument($fileInfo): Document
    {
        if (!$fileInfo instanceof SplFileInfo){
            $fileInfo = $this->factoryFileInfo($fileInfo);
        }

        $data = $this->resolveFileData($fileInfo);

        return $this->factoryDocumentFromFileData($data);
    }

    public function factoryDocumentFromFileData(array $fileData): Document
    {
        $parent = $this->nodeHelper->resolvParentDocument($fileData['parent']);

        if (true === $this->versionable) {
            $file = new DocumentVersionable();
        } else {
            $file = new Document();
        }

        $file->setFileContentFromFilesystem($fileData['path']);
        $file->setNodename($fileData['name']);
        $file->setParentDocument($parent);

        return $file;
    }

    public function factoryFileInfo(string $path): SplFileInfo
    {
        $fileInfo = new SplFileInfo($path);

        return $fileInfo;
    }

    public function resolveFileData(SplFileInfo $fileInfo): array
    {
        $list = [
            'path' => $fileInfo->getRealPath(),
        ];
        $fx = explode($this->splitter, $fileInfo->getPathName());
        $list['full'] = end($fx);
        $nx = explode('/', $list['full']);
        $list['name'] = array_pop($nx);
        $list['parent'] = implode('/', $nx);

        return $list;
    }
}
