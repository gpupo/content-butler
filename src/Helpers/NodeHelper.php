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
use Doctrine\ODM\PHPCR\Document\Generic;

class NodeHelper
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function createParentDocument(string $path, bool $recursive = true): Generic
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


    public function resolvParentDocument(string $path): Generic
    {
        $parent = $this->documentManager->find(null, $path);

        if (empty($parent)) {
            return $this->createParentDocument($path);
        }

        return $parent;
    }

}
