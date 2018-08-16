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


use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCR;

/**
 * @PHPCR\Document
 */
class Document
{
    /**
     * @PHPCR\Id
     */
    private $id;

    /**
     * @PHPCR\ParentDocument
     */
    private $parent;

    /**
     * @PHPCR\Nodename
     */
    private $name;

    /**
     * @PHPCR\Children
     */
    private $children;

    /**
     * @PHPCR\Field(type="string")
     */
    private $title;

    /**
     * @PHPCR\Field(type="string")
     */
    private $content;

    public function getId()
    {
        return $this->id;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }
}
