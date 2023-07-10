<?php

namespace App\OptionsResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;

class PostOptionsResolver extends OptionsResolver
{
    public function configureTitle(bool $isRequired = true): self
    {
        $this->setDefined("title")->setAllowedTypes("title", "string");

        if($isRequired) {
            $this->setRequired("title");
        }

        return $this;
    }

    public function configureContent(bool $isRequired = true): self
    {
        $this->setDefined("content")->setAllowedTypes("content", "string");

        if($isRequired) {
            $this->setRequired("content");
        }

        return $this;
    }

    public function configureCategoryId(bool $isRequired = true): self
    {
        $this->setDefined("category_id")->setAllowedTypes("category_id", "int");

        if($isRequired) {
            $this->setRequired("category_id");
        }

        return $this;
    }
}
