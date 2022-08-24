<?php
declare(strict_types=1);

namespace Emico\Tweakwise\Model;

class ConfigAttributeProcessService
{
    /**
     * @param string|null $filterList
     * @return array
     */
    public static function extractFilterValuesWhitelist(?string $filterList = null): array
    {
        if (empty($filterList)) {
            return [];
        }

        $filterList = trim($filterList);

        $filterListExploded = explode(',', $filterList) ?: [];
        if (empty($filterListExploded)) {
            return [];
        }

        $return = [];
        foreach ($filterListExploded as $listItem) {
            $item = explode('=', trim($listItem)) ?: null;
            
            if ($item === null || !isset($item[0]) || !isset($item[1])) {
                continue;
            }
            $return[$item[0]][] = $item[1];
        }

        return $return;
    }

    /**
     * @param string|null $filterList
     * @return array
     */
    public static function extractFilterWhitelist(?string $filterList = null): array
    {
        if (empty($filterList)) {
            return [];
        }

        $filterList = trim($filterList);

        $filterListExploded = explode(',', $filterList) ?: [];

        if (empty($filterListExploded)) {
            return [];
        }

        return $filterListExploded;
    }
}
