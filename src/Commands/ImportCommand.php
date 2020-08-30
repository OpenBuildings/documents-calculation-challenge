<?php

namespace App\Commands;

use App\Exceptions\CurrencyException;
use App\Exceptions\DocumentException as DocumentExceptionAlias;
use App\Exceptions\FileParserException;
use App\Models\Domain\Calculator\CalculatorBuilder;
use App\Models\Domain\FileParsers\ParserFactory;
use App\Models\Value\Column;
use App\Models\Value\Currency;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    protected static $defaultName = 'import';

    protected function configure()
    {
        $this
            ->setDescription('Sums invoice documents')
            ->setHelp('Sums invoice documents in different currencies with the ability to filter the results.')
            ->addArgument('filename', InputArgument::REQUIRED, 'Path to the CSV file containing the documents.')
            ->addArgument('outputCurrency', InputArgument::REQUIRED, 'Currency in which the output will be given.')
            ->addArgument(
                'currency2Rate',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'List of currencies with their corresponding exchange rates.'
            )
            ->addOption('customer', null, InputArgument::OPTIONAL, 'Filter by customer name.')
            ->addOption('vat', null, InputArgument::OPTIONAL, 'Filter by vat number.')
            ->addOption('document', null, InputArgument::OPTIONAL, 'Filter by document number.')
            ->addOption('type', null, InputArgument::OPTIONAL, 'Filter by document type.')
            ->addOption('parent_document', null, InputArgument::OPTIONAL, 'Filter by parent document number.')
            ->addOption('currency', null, InputArgument::OPTIONAL, 'Filter by currency.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = $this->getData($input->getArgument('filename'));
        $filters = $this->getFilters($input->getOptions());
        $currencies = $this->getCurrencies($input->getArgument('currency2Rate'));
        $outputCurrency = new Currency($input->getArgument('outputCurrency'));

        $calculator = (new CalculatorBuilder())
            ->setData($data)
            ->setCurrencies($currencies)
            ->setFilters($filters)
            ->setOutputCurrency($outputCurrency)
            ->build();

        $totals = $calculator->getTotals();

        foreach ($totals as $customer => $amount) {
            $output->writeln($customer . ' - ' . $amount . ' ' . $outputCurrency->getCode());
        }

        return Command::SUCCESS;
    }

    /**
     * Builds a list of currencies from the currency2rate input argument.
     *
     * @param array $currency2Rate
     * @return Currency[]
     * @throws CurrencyException
     */
    private function getCurrencies(array $currency2Rate): array
    {
        $currencies = [];
        foreach ($currency2Rate as $item) {
            $parts = explode(':', $item);
            if (count($parts) != 2) {
                throw new InvalidArgumentException(
                    $item . ' is not a valid currency format. Expected format is currency:rate, for e.g. USD:0.89'
                );
            }

            list($code, $rate) = $parts;
            $currencies[] = new Currency($code, $rate);
        }
        return $currencies;
    }

    /**
     * Returns parsed data from a file given in an input argument.
     *
     * @param string $filename
     * @return array
     * @throws FileParserException
     */
    private function getData(string $filename): array
    {
        $parser = ParserFactory::create($filename);
        return $parser->parse($filename);
    }

    /**
     * Builds a list of columns and a column value to filter by from input options.
     *
     * @param array $options
     * @return Column[]
     * @throws DocumentExceptionAlias
     */
    private function getFilters(array $options): array
    {
        $option2Column = $this->getOption2Column();

        $filters = [];
        foreach ($options as $key => $value) {
            if ($value && isset($option2Column[$key])) {
                $filters[] = new Column($option2Column[$key], $value);
            }
        }

        return $filters;
    }

    /**
     * Returns input option to column mapping.
     *
     * @return array
     */
    private function getOption2Column(): array
    {
        return [
            'customer' => Column::CUSTOMER,
            'vat' => Column::VAT,
            'document' => Column::DOCUMENT,
            'type' => Column::TYPE,
            'parent_document' => Column::PARENT_DOCUMENT,
            'currency' => Column::CURRENCY,
        ];
    }
}
