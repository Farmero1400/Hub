<?php

declare(strict_types=1);

namespace Farmero\hub;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\world\Position;

use Farmero\hub\Command\HubCommand;
use Farmero\hub\Command\SetHubCommand;

class Hub extends PluginBase
{
    private static Hub $main;
    private ?Position $hubLocation = null;

    public function onEnable(): void
    {
        self::$main = $this;
        $this->saveDefaultConfig();
        
        $this->getServer()->getCommandMap()->register("Hub", new HubCommand());
        $this->getServer()->getCommandMap()->register("SetHub", new SetHubCommand());
    }

    public static function getConfigReplace(string $path, array $replace = [], array $replacer = []): string
    {
        $return = str_replace("{prefix}", self::getConfigValue("prefix"), self::getConfigValue($path));
        return str_replace($replace, $replacer, $return);
    }

    public static function getConfigValue(string $path): array|bool|int|string
    {
        $config = new Config(self::$main->getDataFolder() . "config.yml", Config::YAML);
        return $config->get($path);
    }

    public static function getInstance(): Hub
    {
        return self::$main;
    }

    public function loadHubLocation(): void
    {
        $hubData = $this->getConfigValue("hub");
        if ($hubData !== false) {
            $world = $this->getServer()->getWorldManager()->getWorldByName($hubData["world"]);
            if ($world !== null) {
                $this->hubLocation = new Position($hubData["x"], $hubData["y"], $hubData["z"], $world);
            }
        }
    }

    public function getHubLocation(): ?Position
    {
        return $this->hubLocation;
    }
}
