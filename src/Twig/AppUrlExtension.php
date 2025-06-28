<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppUrlExtension extends AbstractExtension
{

    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    )
    {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_url', [$this, 'getAppUrl']),
        ];
    }

    /**
     * @return string
     */
    public function getAppUrl(): string
    {
        return $this->parameterBag->get('app_base_url');
    }

}
