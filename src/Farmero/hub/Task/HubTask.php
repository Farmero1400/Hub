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

        if ($player->getPosition()->getFloorX() === $this->startposition->getFloorX() and
            $player->getPosition()->getFloorY() === $this->startposition->getFloorY() and
            $player->getPosition()->getFloorZ() === $this->startposition->getFloorZ()) {
            $player->sendTip(Hub::getConfigReplace("cooldown", ["{time}"], [$this->timer]));
            $this->timer--;
        } else {
            $player->sendMessage(Hub::getConfigReplace("cancel"));
            $player->getEffects()->remove(VanillaEffects::BLINDNESS());
            $this->getHandler()->cancel();
            return;
        }

        if ($this->timer === 0) {
            $player->getEffects()->remove(VanillaEffects::BLINDNESS());
            $player->teleport($player->getWorld()->getSafeSpawn());
            $player->sendTip(Hub::getConfigReplace("teleportation"));
            $this->getHandler()->cancel();
            return;
        }
    }
}