<?php

declare(strict_types=1);

namespace Farmero\hub\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Farmero\hub\Hub;

class SetHubCommand extends Command
{
    public function __construct()
    {
        $command = explode(":", Hub::getConfigValue("sethub_command"));
        parent::__construct($command[0]);
        if (isset($command[1])) {
            $this->setDescription($command[1]);
        }
        $this->setAliases(Hub::getConfigValue("sethub_command_aliases"));
        $this->setPermission("hub.sethub");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return false;
        }

        if (!$this->testPermission($sender)) {
            return false;
        }

        $position = $sender->getPosition();
        $world = $position->getWorld();

        $hubData = [
            "x" => $position->getX(),
            "y" => $position->getY(),
            "z" => $position->getZ(),
            "world" => $world->getFolderName()
        ];

        Hub::getInstance()->getConfig()->set("hub", $hubData);
        Hub::getInstance()->getConfig()->save();
        Hub::getInstance()->loadHubLocation(); // Reload the hub location

        $sender->sendMessage(Hub::getConfigReplace("sethub_success"));
        return true;
    }
}
