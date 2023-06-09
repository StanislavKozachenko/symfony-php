<?php
namespace Helpers\S3\S3;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class AwsServices
{
    public static function convertToCSV($array){
        $fs = fopen('products.csv', 'w');
        foreach ($array as $item) {
            $services = "";
            foreach ($item->getServices() as $service){
                $services .= $service->getId() . '|' . $service->getName() . '|';
            }
            $result = array(
                "name"=>$item->getName(),
                "cost"=>$item->getCost(),
                "description"=>$item->getDescription(),
                "customer"=>$item->getCustomer()->getName(),
                "services"=>$services,
                "releaseDate"=>$item->getReleaseDate()->format('Y-m-d H:i:s')
            );
            fputcsv($fs, $result);
        }
        fclose($fs);
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