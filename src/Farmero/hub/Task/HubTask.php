<?php

declare(strict_types=1);

namespace Farmero\hub\Task;

use pocketmine\entity\effect\VanillaEffects;
use pocketmine\scheduler\Task;
use pocketmine\world\Position;
use pocketmine\player\Player;
use Farmero\hub\Hub;

class HubTask extends Task
{
    private Position $startposition;
    private Player $player;
    private int $timer;

    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->startposition = $player->getPosition();
        $this->timer = Hub::getConfigValue("delay");
        Hub::getInstance()->getScheduler()->scheduleDelayedRepeatingTask($this, 20, 20);
    }

    public function onRun(): void
    {
        $player = $this->player;
        if (!$player->isOnline()) {
            $this->getHandler()->cancel();
            return;
        }

        if ($player->getPosition()->equals($this->startposition)) {
            $player->sendTip(Hub::getConfigReplace("cooldown", ["{time}"], [(string)$this->timer]));
            $this->timer--;
        } else {
            $player->sendMessage(Hub::getConfigReplace("cancel"));
            $player->getEffects()->remove(VanillaEffects::BLINDNESS());
            $this->getHandler()->cancel();
            return;
        }

        if ($this->timer === 0) {
            $player->getEffects()->remove(VanillaEffects::BLINDNESS());
            $hubLocation = Hub::getInstance()->getHubLocation();
            if ($hubLocation !== null) {
                $player->teleport($hubLocation);
                $player->sendTip(Hub::getConfigReplace("teleportation"));
            } else {
                $player->sendMessage(Hub::getConfigReplace("no_hub_set"));
            }
            $this->getHandler()->cancel();
        }
    }
}
