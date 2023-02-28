<?php

namespace App\Command\Geolocation;

use App\Repository\UserRepository;
use App\Services\GeolocationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:geolocation:update:users',
    description: 'Update users locations from their IPs',
    hidden: false
)]
class UpdateUsersLocationCommand extends Command
{
    protected static $defaultDescription = 'Update users locations from their IPs';
    private GeolocationService $geolocationService;
    private UserRepository $userRepository;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->geolocationService = new GeolocationService();
        $this->userRepository = new UserRepository();
    }

    protected function configure()
    {
        $this->setHelp("Retrieves and update users locations from ip-api.com API given users IPs");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ipAddresses = $this->userRepository->getFieldValueFromAllUsers('ip') ?? [];

        $output->writeln("Querying IP-API to retrieve locations from users IPs...");

        $locations = $this->geolocationService->getLocationFromIp(array_values($ipAddresses));

        $users = $this->userRepository->findAll();

        if( !empty($users) ){
            foreach ($users as $key => $user){
                $users[$key]['ip_region'] = $locations[ $user['ip'] ];
            }
        }

        $this->userRepository->persist($users);

        $output->writeln("Users locations updates successfully");

        return Command::SUCCESS;
    }
}