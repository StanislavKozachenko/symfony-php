<?php


class AwsServiceTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->awsService = $this->getMockBuilder(\Helpers\AwsServices::class)
            ->onlyMethods(['convertToCsv'])
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
        $this->assertEquals($this->awsService::convertToCSV($input), $expected);
    }

    /**
     * @return iterable<array>
     */
    public static function objectProvider(): iterable
    {
        // string
        yield ['random string', 'random string'];

        // integer
        yield [1, 1];

        // bool
        yield [false, false];

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
            array($item),
            array
            (
                "name"=>"Stan",
                "cost"=>12,
                "description"=>"Description",
                "customer"=>"Customer",
                "services"=>"Name|",
            )
        ];
    }
}