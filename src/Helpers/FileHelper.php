<?php

namespace Helpers;

use PHPUnit\Framework\Exception;
use function PHPUnit\Framework\throwException;

class FileHelper
{
    public function writeToFile(mixed $body, string $filename)
    {
        $fs = fopen($filename, 'w');
        foreach ($body as $item) {
            fputcsv($fs, $item);
        }
        fclose($fs);
    }
    public function convertToCSV(array $array): array|string
    {
        $result = [];
        foreach ($array as $item) {
            try {
                $services = "";
                foreach ($item->getServices() as $service) {
                    //$services .= $service->getId() . '|' . $service->getName() . '|';
                    $services .= $service->getName() . ' |';
                }
                $newItem = array(
                    "name" => $item->getName(),
                    "cost" => $item->getCost(),
                    "description" => $item->getDescription(),
                    "customer" => $item->getCustomer()->getName(),
                    "services" => $services,
                    //"releaseDate"=>$item->getReleaseDate()->format('Y-m-d H:i:s')
                );
                $result[] = $newItem;
            } catch (Exception $e) {
                throwException($e)->toString();
            }
        }
        return $result;
    }
}