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

namespace Gpupo\ContentButler\Document;

trait HierarchyTrait
{
    public function getEndpoint()
    {
        return sprintf('%s/%s', $this->resolveEndpointObject($this->getParentDocument()), $this->getNodename());
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'nodeName' => $this->getNodename(),
            'endpoint' => $this->getEndpoint(),
        ];
    }

    protected function resolveEndpointObject($object)
    {
        if ($object instanceof HierarchyInterface) {
            return $object->getEndpoint();
        }

        $nodes = [];

        while (!empty($object)) {
            $nodes[] = $object->getNodename();
            $object = $object->getParentDocument();
        }

        return implode('/', $nodes);
    }
}
