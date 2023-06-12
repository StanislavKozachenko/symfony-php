<?php

use Helpers\AwsServices;

require_once (__DIR__ . "/../src/Helpers/AwsServices.php");

/**
 * @uses \Helpers\AwsServices
 * (optional)@covers \Helpers\AwsServices::__construct
 */

class AwsServiceTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->awsService = $this->getMockBuilder(AwsServices::class)
            ->onlyMethods(['convertToCSV'])
            ->getMock();
    }

    /**
     * @dataProvider objectProvider
     *
     * @param mixed $input
     * @param mixed $expected
     */
    public function testNormalizeCSVConvert(mixed $input, mixed $expected): void
    {
        $result = $this->awsService->convertToCSV($input);
        $this->assertEquals($result, $expected);
    }

    /**
     * @return iterable<array>
     */
    public static function objectProvider(): iterable
    {
        // string
        yield [['random string'], ''];

        // integer
        yield [[1], ''];

        // bool
        yield [[false], ''];

        // object
        $customer = new \App\Entity\Customer();
        $customer->setName("Customer");

        $service = new \App\Entity\Service();
        $service->setName('Name');

        $item = new \App\Entity\Product();

        $item->setName("Stan");
        $item->setDescription("Description");
        $item->addService($service);
        $item->setCost(12);

        yield [
            [$item, $item],
            ''
        ];
    }
}