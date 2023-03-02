<?php

namespace App\Command\Geolocation;

use App\Repository\UserRepository;
use App\Repository\UsersIpRepository;
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
    private UsersIpRepository $usersIpRepository;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->geolocationService = new GeolocationService();
        $this->userRepository = new UserRepository();
        $this->usersIpRepository = new UsersIpRepository();
    }

    protected function configure()
    {
        $this->setHelp("Retrieves and update users locations from ip-api.com API given users IPs");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $usersIpMap = [];
        $ipAddresses = $this->userRepository->getFieldValueFromAllUsers('ip') ?? [];
        $users = $this->userRepository->findAll();
        $lastIpUpdated = $this->usersIpRepository->findAll();

        $ipAddresses = array_map(function ($userIp) use ($ipAddresses, $users, &$usersIpMap) {
            if(!in_array($userIp['ip'], $ipAddresses)){
                $newIp = $users[$userIp['userId']]['ip'];
                $usersIpMap[$newIp] = $userIp['userId'];
                return $users[$userIp['userId']]['ip'];
            }
            else{
                return null;
            }
        }, $lastIpUpdated);

        $ipAddresses = array_merge($ipAddresses, $this->getUsersIpToAdd($users, $usersIpMap));

        if(empty($usersIpMap)){
            $output->writeln("No IP has changed since last update. Skipping...");
            return Command::SUCCESS;
        }

        $output->writeln("Querying IP-API to retrieve locations from users IPs...");

        $locations = $this->geolocationService->getLocationFromIp(array_values($ipAddresses));

        if(!empty($locations)){
            foreach ($locations as $ip => $location){
                $users[$usersIpMap[$ip]]['ip_region'] = $location;
                $lastIpUpdated[$usersIpMap[$ip]]['ip'] = $ip;
            }
            $this->userRepository->persist($users);
            $this->usersIpRepository->persist($lastIpUpdated);

            $output->writeln(sprintf("Updated locations for %s users", count($usersIpMap)));
            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }

    /**
     * Get IPs from users who doesn't have location set and they never have been updated their location before (new users)
     * @param array $users
     * @param array $usersIpMap
     * @return array
     */
    private function getUsersIpToAdd(array $users, array &$usersIpMap): array
    {
        return array_map(function ($user) use (&$usersIpMap){
            if( empty( $user['ip_region'] )){
                $usersIpMap[$user['ip']] = $user['id'];
                return $user['ip'];
            }
            else return null;
        }, $users);
    }
}