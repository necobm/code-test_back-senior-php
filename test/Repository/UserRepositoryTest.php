<?php

use App\Repository\UserRepository;
class UserRepositoryTest extends \PHPUnit\Framework\TestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = new UserRepository(
            __DIR__ . '/../../resources/db_test/users.json'
        );
    }

    public function testGetNameFromAllUsersWithCorrectParams()
    {
        $res = $this->userRepository->getFieldValueFromAllUsers('name');
        $this->assertIsArray($res);
        $this->assertCount(5, $res);
        $this->assertContains("Sergio Palma", $res);
    }

    public function testGetIpFromAllUsersWithCorrectParams()
    {
        $res = $this->userRepository->getFieldValueFromAllUsers('ip');
        $this->assertIsArray($res);
        $this->assertCount(5, $res);
        $this->assertContains("83.223.227.125", $res);
    }

    public function testGetIdFromAllUsersWithCorrectParams()
    {
        $res = $this->userRepository->getFieldValueFromAllUsers('id');
        $this->assertIsArray($res);
        $this->assertCount(5, $res);
        $this->assertContains(1, $res);
    }

    public function testGetFieldValueFromAllUsersWithInvalidParams()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid field");
        $res = $this->userRepository->getFieldValueFromAllUsers('email');
    }

    public function testGetFieldValueFromAllUsersWithInvalidDataSource()
    {
        $invalidUserRepository = new UserRepository("invalid/dir/datasources.json");
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Wrong data file");
        $res = $invalidUserRepository->getFieldValueFromAllUsers('id');
    }

    public function testFindAll()
    {
        $res = $this->userRepository->findAll();
        $this->assertIsArray($res);
    }

    public function testFindOneById()
    {
        $res = $this->userRepository->findOneById(1);
        $this->assertIsArray($res);
        $this->assertArrayHasKey('name', $res);
        $this->assertArrayHasKey('ip', $res);
        $this->assertArrayHasKey('ip_region', $res);
        $this->assertContains('Sergio Palma', $res);
    }

    public function testFindOneByIdWithWrongId()
    {
        $res = $this->userRepository->findOneById(1000);
        $this->assertNull($res);
    }

    public function testPersistWithEmptyData()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("You cannot persist empty data, you may loose all your data by accident");
        $this->userRepository->persist([]);
    }

    public function testUpdateWithInvalidId()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("The given id doesn't exist");
        $this->userRepository->update(1000, ["name"=>"Pedro"]);
    }

    public function testUpdateWithEmptyData()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("WARNING, you are updating a record with empty data");
        $this->userRepository->update(1, []);
    }

}