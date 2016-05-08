<?php
namespace BrawlTDM\commands;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat as TF;
use BrawlTDM\Main;
class commands{
	
	private $plugin;
	
	public function __construct(Main $plugin){
		$this->plugin = $plugin;
		$this->settings = $this->plugin->settings;
		$this->map = $this->plugin->map;
		$this->areans = $this->plugin->areans;
	}
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		if(strtolower($cmd->getName()) === "btdm"){
			if(isset($args[0])){
				if($args[0] === "set"){
					if(isset($args[1])){
						$sender->sendMessage(TF::GREEN."Please tap a sign to set a BrawlTDM areana for ".$args[1]."!");
						$this->map[$sender->getName()] = $args[1];
						$this->plugin->setter[$sender->getName()] = 0;
					}else{
						$sender->sendMessage(TF::RED."Usage /btdm set [arena name]");
					}
				}
				if($args[0] === "reload"){
					$this->settings->reload();
					$this->areans->reload();
					$sender->sendMessage(TF::GREEN."Configs reloaded!");
				}
				if($args[0] === "help"){
					$sender->sendMessage(TF::RED."--------".TF::DARK_PURPLE."TeamDeathMatch".TF::RED."--------\n".TF::GOLD."BrawlTDM plugin by EnderBoxie and Epicrafter60".TF::AQUA."(Twitter @EnderBoxie and @Epicrafter60)");
					$sender->sendMessage(TF::GREEN."/btdm set [arena name]");
					$sender->sendMessage(TF::GREEN."/btdm reload");
					$sender->sendMessage(TF::RED."------------------------------");
				}
			}else{
					$sender->sendMessage(TF::RED."--------".TF::DARK_PURPLE."TeamDeathMatch".TF::RED."--------\n".TF::GOLD."BrawlTDM plugin by EnderBoxie and Epicrafter60".TF::AQUA."(Twitter @EnderBoxie and @Epicrater_60)")
					$sender->sendMessage(TF::GREEN."/btdm set [arena name]");
					$sender->sendMessage(TF::GREEN."/btdm reload");
					$sender->sendMessage(TF::RED."------------------------------");
			}
			
		}
	}
}
