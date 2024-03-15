<?php

namespace DVC\AdjustPlentaJobsSitemapByEmploymentType\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use DVC\AdjustPlentaJobsSitemapByEmploymentType\AdjustPlentaJobsSitemapByEmploymentTypeBundle;
use Plenta\ContaoJobsBasic\PlentaContaoJobsBasicBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(AdjustPlentaJobsSitemapByEmploymentTypeBundle::class)
                ->setLoadAfter([
                    ContaoCoreBundle::class,
                    PlentaContaoJobsBasicBundle::class,
                ])
        ];
    }
}
