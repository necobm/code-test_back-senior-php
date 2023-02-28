<?php

namespace App\Command\Geolocation;

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

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->geolocationService = new GeolocationService();
    }

    protected function configure()
    {
        $this->setHelp("Retrieves and update users locations from ip-api.com API given users IPs");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->geolocationService->getLocationFromIp(["77.162.109.160"]));

        return Command::SUCCESS;
    }
}