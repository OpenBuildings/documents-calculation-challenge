<?php

namespace Finance\Command;

use Finance\Model\Calculator;
use Finance\Model\CurrencyConverter;
use Finance\Model\Data\CsvDataSource;
use Finance\Model\Invoices;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    const ARG_FILEPATH = 'filepath';
    const ARG_RATES = 'rates';
    const ARG_CURRENCY = 'currency';
    const OPT_VAT = 'vat';

    protected static $defaultName = 'import';

    protected function configure()
    {
        $this
            ->setDescription('Command for calculating invoices total.')
            ->setHelp('This command allows you to sum invoices in desired currency')
            ->addArgument(self::ARG_FILEPATH, InputArgument::REQUIRED, 'The CSV file containing all invoices.')
            ->addArgument(self::ARG_RATES, InputArgument::REQUIRED, 'The exchange rates')
            ->addArgument(self::ARG_CURRENCY, InputArgument::REQUIRED, 'Target currency')
            ->addOption(self::OPT_VAT, null, InputOption::VALUE_OPTIONAL, "Filter by VAT");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $csvDataSource = new CsvDataSource($input->getArgument(self::ARG_FILEPATH));
            $invoices = new Invoices($csvDataSource);
            $currencyConverter = new CurrencyConverter($input->getArgument(self::ARG_RATES));
            $outputCurrency = $input->getArgument(self::ARG_CURRENCY);
            $vat = (string)$input->getOption(self::OPT_VAT);

            $calculator = new Calculator($invoices, $currencyConverter, $outputCurrency);
            foreach ($calculator->getTotals($vat) as $total) {
                $output->writeln(sprintf('%s - %.2f %s',
                    $total[Calculator::CUSTOMER],
                    $total[Calculator::TOTAL],
                    $total[Calculator::CURRENCY]
                ));
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
