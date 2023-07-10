<?php

namespace App\OptionsResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryOptionsResolver extends OptionsResolver
{
    public function configureName(bool $isRequired = true): self
    {
        $this->setDefined("name")->setAllowedTypes("name", "string");

        if($isRequired) {
            $this->setRequired("name");
        }

        return $this;
    }
}
