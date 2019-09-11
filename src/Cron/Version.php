<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */
namespace Emico\Tweakwise\Cron;

use Magento\Framework\Composer\ComposerInformation;
use Magento\Framework\App\Config\Storage\WriterInterface;

/**
 * Class Version
 * @package Emico\Tweakwise\Cron
 */
class Version
{
    /**
     * @var ComposerInformation
     */
    protected $composerInformation;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * Version constructor.
     * @param ComposerInformation $composerInformation
     * @param WriterInterface $configWriter
     */
    public function __construct(
        ComposerInformation $composerInformation,
        WriterInterface $configWriter
    ) {
        $this->composerInformation = $composerInformation;
        $this->configWriter = $configWriter;
    }

    /**
     * Update Tweakwise version number to config table
     */
    public function execute()
    {
        $installedPackages = $this->composerInformation
            ->getInstalledMagentoPackages();

        if (!isset($installedPackages['emico/tweakwise']['version'])) {
            // This should never be the case
            return;
        }

        $version = $installedPackages['emico/tweakwise']['version'];
        $userAgentString = sprintf(
            '%s(%s)',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36 Magento2Tweakwise',
            $version
            );
        $this->configWriter->save('tweakwise/general/version', $userAgentString);
    }
}