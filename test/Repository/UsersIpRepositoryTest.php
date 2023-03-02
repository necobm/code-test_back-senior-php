<?php

use App\Repository\UsersIpRepository;
class UsersIpRepositoryTest extends \PHPUnit\Framework\TestCase
{
    private UsersIpRepository $usersIpRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usersIpRepository = new UsersIpRepository(
            __DIR__ . '/../../resources/db_test/users_ip.json'
        );
    }

    public function testFindAll()
    {
        $res = $this->usersIpRepository->findAll();
        $this->assertIsArray($res);
    }

    public function testFindOneById()
    {
        $res = $this->usersIpRepository->findOneById(1);
        $this->assertIsArray($res);
        $this->assertArrayHasKey('ip', $res);
        $this->assertContains('83.223.227.125', $res);
    }

    public function testFindOneByIdWithWrongId()
    {
        $res = $this->usersIpRepository->findOneById(1000);
        $this->assertNull($res);
    }

    public function testPersistWithEmptyData()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("You cannot persist empty data, you may loose all your data by accident");
        $this->usersIpRepository->persist([]);
    }

    public function testUpdateWithInvalidId()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("The given id doesn't exist");
        $this->usersIpRepository->update(1000, ["ip"=>"127.0.0.1"]);
    }

    public function testUpdateWithEmptyData()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("WARNING, you are updating a record with empty data");
        $this->usersIpRepository->update(1, []);
    }

}