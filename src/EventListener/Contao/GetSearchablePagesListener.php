<?php

namespace DVC\AdjustPlentaJobsSitemapByEmploymentType\EventListener\Contao;

use Contao\Config;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;

/**
 * @Hook("getSearchablePages")
 */
class GetSearchablePagesListener
{
    public function __invoke(array $pages, $rootId = null, bool $isSitemap = false, string $language = null): array
    {
        foreach ($this->getPagesToAdd($language) as $page) {
            $pages[] = $page;
        }

        return $pages;
    }

    private function getPagesToAdd(string $language): array
    {
        $jobs = PlentaJobsBasicOfferModel::findAllPublished();
        
        if (empty($jobs)) {
            return [];
        }

        $pages = [];

        foreach ($jobs as $job) {
            if ($job->robots === 'noindex,nofollow') {
                continue;
            }

            if ($page = $this->getAbsoluteUrl($job, $language)) {
                $pages[] = $page;
            }
        }

        return $pages;
    }

    private function getReaderPage(PlentaJobsBasicOfferModel $job, string $language): ?PageModel
    {
        $modules = ModuleModel::findByType('plenta_jobs_basic_offer_list');

        if (!$modules) {
            return null;
        }

        $page = null;

        foreach ($modules as $module) {
            $jobLocations = StringUtil::deserialize($job->jobLocation);
            $jobEmploymentTypes = json_decode($job->employmentType);
            $moduleLocations = StringUtil::deserialize($module->plentaJobsBasicLocations);
            $moduleEmploymentTypes = StringUtil::deserialize($module->plentaJobsBasicEmploymentTypes);
            
            $moduleMatchesLocation = false;
            $moduleMatchesEmploymentType = false;

            if (\is_array($jobLocations) && \is_array($moduleLocations)) {
                $moduleMatchesLocation = count(\array_intersect($moduleLocations, $jobLocations)) > 0;
            }

            if (\is_array($jobEmploymentTypes) && \is_array($moduleEmploymentTypes)) {
                $moduleMatchesEmploymentType = count(\array_intersect($moduleEmploymentTypes, $jobEmploymentTypes)) > 0;
            }

            if ($moduleMatchesLocation || $moduleMatchesEmploymentType) {
                $jumpTp = PageModel::findWithDetails($module->jumpTo);

                if ($jumpTp->rootLanguage === $language) {
                    $page = $jumpTp;
                    break;
                }
            }
        }

        return $page;
    }

    private function getAbsoluteUrl(PlentaJobsBasicOfferModel $job, string $language): ?string
    {
        $objPage = $this->getReaderPage($job, $language);
        if (!$objPage) {
            return null;
        }
        $params = $this->getParams($job, $language);

        return StringUtil::ampersand($objPage->getAbsoluteUrl($params));
    }

    private function getParams(PlentaJobsBasicOfferModel $job, string $language)
    {
        $alias = $job->alias;
        if ($translation = $job->getTranslation($language)) {
            $alias = $translation['alias'];
        }

        return (Config::get('useAutoItem') ? '/' : '/items/').($alias ?: $job->id);
    }
}
