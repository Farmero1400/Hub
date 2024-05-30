<?php

declare(strict_types=1);

namespace Farmero\hub\Command;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\player\Player;

use Farmero\hub\Hub;

class HubCommand extends Command
{
    public function __construct()
    {
        $command = explode(":", Hub::getConfigValue("command"));
        parent::__construct($command[0]);
        if (isset($command[1])) $this->setDescription($command[1]);
        $this->setAliases(Hub::getConfigValue("command_aliases"));
        $this->setPermission("hub.cmd");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $command = explode(":", "command");
            if ((isset($command[2])) and !($sender->hasPermission($command[2]))) return;

            if ($sender->hasPermission("hub.cmd")) {
                $sender->teleport($sender->getWorld()->getSafeSpawn());
                $sender->sendTip(Hub::getConfigReplace("teleportation"));
            } else {
                $sender->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 20 * (Hub::getConfigValue("delay") + 2), 10));
                new SpawnTask($sender);
            }
        }
    }
}