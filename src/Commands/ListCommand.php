<?php
/**
 * @package: chapi
 *
 * @author:  msiebeneicher
 * @since:   2015-07-28
 *
 */

namespace Chapi\Commands;

use Chapi\Service\JobRepository\JobRepositoryServiceInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ListCommand extends AbstractCommand
{
    const DEFAULT_VALUE_JOB_NAME = 'all';

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('list')
            ->setDescription('Display your job(s) and filter they by status')
            ->addArgument('jobName', InputArgument::OPTIONAL, 'display a specific job', self::DEFAULT_VALUE_JOB_NAME)
            ->addOption('onlyFailed', 'f', InputOption::VALUE_NONE, 'Display only failed jobs')
        ;
    }

    /**
     *
     */
    protected function process()
    {
        /** @var JobRepositoryServiceInterface  $_oJobRepositoryChronos */
        $_oJobRepositoryChronos = $this->getContainer()->get(JobRepositoryServiceInterface::DIC_NAME_CHRONOS);

        $_sJobName = $this->oInput->getArgument('jobName');
        $_bOnlyFailed = (bool) $this->oInput->getOption('onlyFailed');

        if (!empty($_sJobName) && $_sJobName != self::DEFAULT_VALUE_JOB_NAME)
        {
            $_aJobData = $_oJobRepositoryChronos->getJob($_sJobName);
            foreach ($_aJobData as $_sKey => $_sValue)
            {
                if (is_array($_sValue))
                {
                    $_sValue = implode(' | ', $_sValue);
                }
                $this->oOutput->writeln(sprintf('<comment>%s:</comment> <info>%s</info>', $_sKey, $_sValue));
            }

        }
        else
        {
            foreach ($_oJobRepositoryChronos->getJobs() as $_aJobData)
            {
                if (
                    ($_bOnlyFailed && $_aJobData->errorsSinceLastSuccess > 0)
                    || $_bOnlyFailed == false
                )
                {
                    $this->oOutput->writeln(sprintf('<comment>%s</comment>', $_aJobData->name));
                }
            }

        }
    }
}