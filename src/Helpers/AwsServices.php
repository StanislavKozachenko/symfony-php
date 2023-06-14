<?php

namespace Helpers;
use Helpers\FileHelper;
class AwsServices
{

    public function init(mixed $body): string
    {
        $fileHelper = new FileHelper();
        $fileHelper->writeToFile($fileHelper->convertToCSV($body), "products.csv");

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