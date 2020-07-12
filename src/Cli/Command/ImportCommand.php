<?php


namespace App\Cli\Command;

use App\Model\CalculationResult;
use App\Model\Currency;
use App\Reader\FileReaderInterface;
use App\Service\CalculationServiceInterface;

/**
 * Class ImportCommand
 * @package App\Command
 */
class ImportCommand implements CliCommandInterface
{
    /** @var CalculationServiceInterface */
    private $calculationService;
    /**
     * @var FileReaderInterface
     */
    private $reader;

    /**
     * ImportCommand constructor.
     * @param CalculationServiceInterface $calculationService
     * @param FileReaderInterface $reader
     */
    public function __construct(CalculationServiceInterface $calculationService, FileReaderInterface $reader)
    {
        $this->calculationService = $calculationService;
        $this->reader = $reader;
    }

    /**
     * @inheritDoc
     */
    public function run($args): array
    {
        list($file, $currencies, $outputCurrency, $vat) = $args;

        if (empty($outputCurrency)) {
            throw new \Exception('No output currency provided');
        }

        $this->calculationService->setOutputCurrency($outputCurrency);
        $this->calculationService->setCurrencies($this->getCurrencies($currencies));
        $this->calculationService->setData($this->getFileData($file));
        $vat = str_replace('--vat=', '', $vat);
        $data = $this->calculationService->getTotals($vat);

        return $this->getCalculationResults($data, $outputCurrency);
    }

    /**
     * @param string $currenciesString
     * @return Currency[]
     */
    private function getCurrencies(string $currenciesString): array
    {
        $currencies = explode(',', $currenciesString);

        return array_map(function ($value) {
            $currency = explode(':', $value);
            if (count($currency) != 2 || is_float($currency[1])) {
                throw new \Exception('Invalid currency format');
            }

            return new Currency($currency[0], $currency[1]);
        }, $currencies);
    }

    /**
     * @param $file
     * @return array
     * @throws \Exception
     */
    private function getFileData($file): array
    {
        return $this->reader->getData(ROOT . "/$file");
    }

    /**
     * @param array $data
     * @param $outputCurrency
     * @return array
     */
    private function getCalculationResults(array $data, $outputCurrency): array
    {
        $result = [];

        foreach ($data as $vendor => $sum) {
            $result[] = new CalculationResult($vendor, $sum, $outputCurrency);
        }

        return $result;
    }
}
