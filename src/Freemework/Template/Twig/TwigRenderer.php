<?php

namespace Freemework\Template\Twig;

use Freemework\Template\TemplateRenderer;
use Twig\Loader\LoaderInterface;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\Extension\AbstractExtension;

class TwigRenderer implements TemplateRenderer
{
	/**
	 * @var Environment $twig Environment
	 */
    private Environment $twig;

	/**
	 * @var array $extensions ExtensionInterface[]
	 */
    private array $extensions;

	/**
	 * @param Environment $twig Environment
     * @param array $extensions ExtensionInterface[]
	 */
    public function __construct(Environment $twig, array $extensions = [])
    {
        $this->twig = $twig;
        $this->extensions = $extensions;

        foreach ($this->extensions as $extension) {
            if ($extension instanceof ExtensionInterface) {
                $this->twig->addExtension($extension);
            }
        }
    }

    public function render($name, array $params = []): string
    {
        return $this->twig->render($name, $params);
    }
}
