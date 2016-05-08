<?php
namespace BrawlTDM\events;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\player\PlayerInteractEvent;
use BrawlTDM\Main;
use pocketmine\utils\TextFormat as TF;
class EventsManager implements Listener{
	
	private $plugin;
	
	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
		$this->settings = $this->plugin->settings;
		$this->map = $this->plugin->map;
		$this->areans = $this->plugin->areans;
	}
	
	public function onInteract(PlayerInteractEvent $ev){
		$p = $ev->getPlayer();
		$block = $ev->getBlock();
		$usrname = $p->getName();
		if(isset($this->plugin->setter[$usrname])){
			switch($this->plugin->setter[$usrname]){
				case 0:
				$this->array = array("x" => $block->getX(),
				"y" => $block->getY(),
				"z" => $block->getZ());
				$this->settings->set("sign[{$this->map[$usrname]}]",$this->array);
				$this->setter[$usrname]++;
				$p->sendMessage(TF::GREEN."Created position! Please select the next one!");
				break;
				
				case 1:
				$this->array = array("x" => $block->getX(),
				"y" => $block->getY(),
				"z" => $block->getZ(),
				"level" => $p->getLevel()->getName());
				$this->settings->set("pos1[{$this->map[$usrname]}]",$this->array);
				$this->setter[$usrname]++;
				$p->sendMessage(TF::GREEN."Created position! Please select the next one!");
				break;
				case 2:
				$this->array = array("x" => $block->getX(),
				"y" => $block->getY(),
				"z" => $block->getZ(),
				"level" => $p->getLevel()->getName());
				$this->settings->set("pos2[{$this->map[$usrname]}]",$this->array);
				$this->settings->save();
				$p->sendMessage(TF::GREEN."All positions done!");
				unset($this->setter[$usrname]);
				unset($this->map[$usrname]);
				break;
			}
		}else{
	$sign = $p->getLevel()->getTile($block);
	foreach($this->areans->get("Maps") as $map){
		$level = $this->plugin->getServer()->getLevelByName($map);
if($ev->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK && $this->settings->exists("pos2[{$map}]") && $sign instanceof \pocketmine\tile\Sign && $sign->getY() === $this->settings->get("sign[{$map}]")["y"] && $sign->getX() === $this->settings->get("sign[{$map}]")["x"] && $sign->getZ() === $this->settings->get("sign[{$map}]")["z"]){
	if(count($level->getPlayers()) === 10){
		$p->sendMessage(TF::RED."Match full!");
		return;
	}else{
		if($this->plugin->pickTeam($map,$p) === false){
			$p->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn());
		}else{
		}
		$p->setLevel($level);
                if(count($this->plugin->redPlayers[$map]) && count($this->plugin->bluePlayers[$map]) === 0){
                    
                }
		$this->plugin->pickTeam($map,$p);
                
		$p->sendMessage($this->plugin->translateColor("&",$this->settings->get("prefix")."you joined a BrawlTDM match on ".$level." map!"));
	}
}
}
	}
	}
	
}
