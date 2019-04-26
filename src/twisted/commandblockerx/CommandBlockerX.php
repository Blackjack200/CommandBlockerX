<?php
declare(strict_types=1);

namespace twisted\commandblockerx;

use function file_exists;
use function implode;
use function is_array;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\RemoteConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use function var_dump;

class CommandBlockerX extends PluginBase implements Listener{

    public function onEnable() : void{
        if(!file_exists($this->getDataFolder() . "config.yml")){
            $this->saveDefaultConfig();
        }
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(!$sender->hasPermission("commandblockerx.command")){
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command");
            return false;
        }
        if(empty($args)){
            $sender->sendMessage(TextFormat::RED . "Use '/commandblocker <reload|info>'");
            return false;
        }
        switch($args[0]){
            case "reload":
                $this->reloadConfig();
                $sender->sendMessage(TextFormat::GREEN . "Successfully reloaded configuration");
                break;
            case "info":
                $description = $this->getDescription();
                $sender->sendMessage(TextFormat::AQUA . "CommandBlockerX " . TextFormat::DARK_GRAY . "[" . TextFormat::AQUA . "v" . $description->getVersion() . TextFormat::DARK_GRAY . "]");
                $sender->sendMessage(TextFormat::AQUA . "Description: " . TextFormat::GRAY . $description->getDescription());
                $sender->sendMessage(TextFormat::AQUA . "Author: " . TextFormat::GRAY . implode(", ", $description->getAuthors()));
                $sender->sendMessage(TextFormat::AQUA . "Website: " . TextFormat::GRAY . $description->getWebsite());
                break;
            default:
                $sender->sendMessage(TextFormat::RED . "Use '/commandblocker <reload|info>'");
                break;
        }
        return true;
    }

    public function onServerCommand(CommandEvent $event) : void{
        $command = $event->getCommand();
        $sender = $event->getSender();
        $blockedCommands = $this->getConfig()->get("blocked-commands", []);
        if(!is_array($blockedCommands)){
            $this->getLogger()->error("blocked-commands key must be an array");
            return;
        }
        $datum = $blockedCommands[$command] ?? null;
        if($datum !== null){
            if(($datum["console"] ?? false) && $sender instanceof ConsoleCommandSender){
                $event->setCancelled();
            }else if(($datum["in-game"] ?? false) && $sender instanceof Player){
                $event->setCancelled();
            }else if(($datum["rcon"] ?? false) && $sender instanceof RemoteConsoleCommandSender){
                $event->setCancelled();
            }
        }
        if($event->isCancelled()){
            $sender->sendMessage($this->getConfig()->getNested("messages.command-blocked", TextFormat::RED . "This command is blocked"));
        }
    }
}