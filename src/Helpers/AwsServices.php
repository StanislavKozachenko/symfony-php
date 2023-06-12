<?php

namespace Helpers;

use Doctrine\DBAL\Driver\Exception;
use function PHPUnit\Framework\throwException;

class AwsServices
{

    public function convertToCSV(array $array): array|string
    {
        $result = [];
        foreach ($array as $item) {
            try {
                $services = "";
                foreach ($item->getServices() as $service) {
//                  $services .= $service->getId() . '|' . $service->getName() . '|';
                    $services .= $service->getName() . ' |';
                }
                $newItem = array(
                    "name" => $item->getName(),
                    "cost" => $item->getCost(),
                    "description" => $item->getDescription(),
                    "customer" => $item->getCustomer()->getName(),
                    "services" => $services,
//                    "releaseDate"=>$item->getReleaseDate()->format('Y-m-d H:i:s')
                );
                $result[] = $newItem;
            } catch (Exception $e) {
                throwException($e)->toString();
            }
        }
        return $result;
    }
    public function writeToFile(mixed $body)
    {
        $fs = fopen('products.csv', 'w');
        foreach ($body as $item) {
            fputcsv($fs, $item);
        }
        fclose($fs);
    }
    public function init(mixed $body): string
    {
        $this->writeToFile($this->convertToCSV($body));

        $bucketName = 'product-bucket';

        $s3 = new \Aws\Sdk([
            'version' => 'latest',
            'region' => 'us-east-1',
            'use_path_style_endpoint' => true,
            'endpoint'=> 'https://localhost.localstack.cloud:4566',
            'credentials'=>[
                'key'=>'test',
                'secret'=>'test'
            ]
        ]);
        $s3Client = $s3->createS3();

        $result = $s3Client->createBucket([
            'Bucket' => $bucketName,
        ]);
        $result = $s3Client->putObject([
            'Bucket' => $bucketName,
            'Key'=>'products.csv',
            'Body' => fopen('products.csv', 'r')
        ]);

        $result = $s3Client->getObject([
            'Bucket' => $bucketName,
            'Key'=>'products.csv',
        ]);
        if($result) {
            $sesClient = $s3->createSes();

            $result = $sesClient->verifyEmailAddress(["EmailAddress"=>"admin@admin.com"]);
            $result = $sesClient->verifyEmailAddress(["EmailAddress"=>"user@user.com"]);

            $result = $sesClient->sendEmail([
                    'Destination' => [
                        'ToAddresses' => ["user@user.com"],
                    ],
                    'Source' => "admin@admin.com",
                    'Message' => [
                        'Body' => [
                            'Text' => [
                                'Charset' => "utf-8",
                                'Data' => "Export success!",
                            ],
                        ],
                        'Subject' => [
                            'Charset' => "utf-8",
                            'Data' => "Export success!",
                        ],
                    ]
            ]);
        }
        return "Success";
    }
}