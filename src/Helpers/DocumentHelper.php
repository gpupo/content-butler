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

class DocumentHelper
{
    protected $documentManager;

    protected $folderHelper;

    protected $splitter;

    protected $versionable;

    public function __construct(DocumentManager $documentManager, string $splitter = './', $versionable = false)
    {
        $this->documentManager = $documentManager;
        $this->folderHelper = new FolderHelper($this->documentManager);
        $this->splitter = $splitter;
        $this->versionable = $versionable;
    }

    public function factoryDocument($fileInfo, $millenialInteger = null, $overshadow = null): Document
    {
        if (empty($millenialInteger)) {
            $data = $this->resolveFileData($fileInfo);
        } else {
            $data = $this->resolveMillenialFileData($fileInfo, $millenialInteger, $overshadow);
        }

        return $this->factoryDocumentFromFileData($data);
    }

    public function factoryDocumentFromFileData(array $fileData): Document
    {
        $parent = $this->folderHelper->resolvParentDocument($fileData['parent']);

        if (empty($this->versionable)) {
            $file = new Document();
        } else {
            $file = new DocumentVersionable();
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

    public function resolveFileData($fileInfo): array
    {
        if (!$fileInfo instanceof SplFileInfo) {
            $fileInfo = $this->factoryFileInfo($fileInfo);
        }

        $list = [
            'path' => $fileInfo->getRealPath(),
        ];
        $fx = explode($this->splitter, $fileInfo->getPathName());
        $list['full'] = end($fx);
        $nx = explode('/', $list['full']);
        $list['name'] = array_pop($nx);
        $list['parent'] = implode('/', $nx);
        $list['extension'] = $fileInfo->getExtension();

        return $list;
    }

    public function resolveMillenialFileData($fileInfo, $millenialInteger, $overshadow = null): array
    {
        $mh = new MillennialHelper();
        $data = $this->resolveFileData($fileInfo);
        $data['parent'] = $mh->calculate($millenialInteger);
        $data['integer'] = $millenialInteger;

        if (!empty($overshadow)) {
            $data['name'] = sprintf('%s.%s', mb_substr(sha1($data['name']), 0, 8), $data['extension']);
        }

        return $data;
    }
}
