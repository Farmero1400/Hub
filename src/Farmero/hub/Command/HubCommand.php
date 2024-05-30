<?php

declare(strict_types=1);

namespace Farmero\hub\Command;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Farmero\hub\Hub;
use Farmero\hub\Task\HubTask;

class HubCommand extends Command
{
    public function __construct()
    {
        $command = explode(":", Hub::getConfigValue("command"));
        parent::__construct($command[0]);
        if (isset($command[1])) {
            $this->setDescription($command[1]);
        }
        $this->setAliases(Hub::getConfigValue("command_aliases"));
        $this->setPermission("hub.cmd");
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

        $sender->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 20 * (Hub::getConfigValue("delay") + 2), 10));
        new HubTask($sender);

        return true;
    }
}
