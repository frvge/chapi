<?php
/**
 * @package: chapi
 *
 * @author:  msiebeneicher
 * @since:   2015-07-30
 *
 */

namespace Chapi\Commands;

use Chapi\Service\JobRepository\JobRepositoryServiceInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;

class InfoCommand extends AbstractCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('info')
            ->setDescription('Display your job information from chronos')
            ->addArgument('jobName', InputArgument::REQUIRED, 'selected job')
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
        $_oJobEntity = $_oJobRepositoryChronos->getJob($_sJobName);

        $this->oOutput->writeln(sprintf("\n<comment>info '%s'</comment>\n", $_oJobEntity->name));

        $_oTable = new Table($this->oOutput);
        $_oTable->setHeaders(array('Property', 'Value'));

        foreach ($_oJobEntity as $_sKey => $_mValue)
        {
            if (is_array($_mValue))
            {
                $_mValue = (!empty($_mValue))
                    ? '[ ' . implode(', ', $_mValue) . ' ]'
                    : '[ ]';
            }
            elseif (is_bool($_mValue))
            {
                $_mValue = ($_mValue == true)
                    ? 'true'
                    : 'false';
            }

            $_oTable->addRow(array($_sKey, $_mValue));
        }

        $_oTable->render();
    }
}