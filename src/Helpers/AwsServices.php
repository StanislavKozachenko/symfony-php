<?php
namespace Helpers;

use Doctrine\DBAL\Driver\Exception;
use function PHPUnit\Framework\throwException;

class AwsServices
{
    public static function convertToCSV($array): array|string
    {
        $fs = fopen('products.csv', 'w');
        $result = [];
        foreach ($array as $item) {
            try {
                $services = "";
                foreach ($item->getServices() as $service){
//                    $services .= $service->getId() . '|' . $service->getName() . '|';
                    $services .= $service->getName() . '|';
                }
                $result = array(
                    "name"=>$item->getName(),
                    "cost"=>$item->getCost(),
                    "description"=>$item->getDescription(),
                    "customer"=>$item->getCustomer()->getName(),
                    "services"=>$services,
//                    "releaseDate"=>$item->getReleaseDate()->format('Y-m-d H:i:s')
                );
            } catch (Exception $e){
                throwException($e);
            }
            fputcsv($fs, $result);
        }
        fclose($fs);
        return $result;
    }

    public static function init($body): string
    {
        self::convertToCSV($body);

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