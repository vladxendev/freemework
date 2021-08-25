<?php

namespace Freemework\Template;

interface TemplateRenderer
{
    public function render($name, array $params = []): string;
}
