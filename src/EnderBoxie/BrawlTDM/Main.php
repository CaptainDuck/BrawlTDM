<?php 
namespace EnderBoxie/BrawlTDM;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use TeamDeathMatch\events\EventsManager;
use TeamDeathMatch\tasks\GameTask;
use TeamDeathMatch\commands\Commands;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
class Main extends PluginBase implements Listener{
public $setter = array();
public $map = array();
public $maps = array();
public $score = array();
public $time = array();
public $redPlayers = array();
public $bluePlayers = array();
public function onEnable(){
	@mkdir($this->getDataFolder());
	$this->settings = new Config($this->getDataFolder()."settings.yml", Config::YAML, array("score-to-win" => 30,"game-time" => 900,"message-type" => "popup","prefix" => "[TDM]","helmet" => array(1,1,1),"chestplate" => array(1,1,1),"leggings" => array(1,1,1),"boots" => array(1,1,1),"weapon" => array(1,1,1),"food" => array(1,1,1),"sign-ready" => array("-----","TDM","Ready","-----"),"sign-queuing" => array("-----","TDM","Queueing","-----"),"sign-running" => array("-----","TDM","Running","-----")),"win-message" => "{PLAYER} won a TeamDeathMatch!");
	$this->areans = new Config($this->getDataFolder()."areans.yml", Config::YAML, array("Maps" => array("Map-1")));
	foreach($this->areans->get("Maps") as $m){
	$this->maps[$m] = $this->settings->get("game-time");	
	}
	echo var_dump($this->maps);
	$this->getServer()->getLogger()->info("[BrawTDM]Loaded");
	$this->getServer()->getPluginManager()->registerEvents(new EventsManager($this),$this);
	$this->getServer()->getScheduler()->scheduleRepeatingTask(new GameTask($this),20); 
	$this->commands = new Commands($this);
}
public function onDisable(){
	$this->getServer()->getLogger()->info("[BrawlTDM]Disabled!");
	$this->settings->save();
}
public function giveItems(Player $player){
	$inv = $player->getInventory();
	
	$inv->setContents([]);
	/* by item id */
	$h = $this->settings->get("helmet");
	$c = $this->settings->get("chestplate");
	$l = $this->settings->get("leggings");
	$b = $this->settings->get("boots");
	$f = $this->settings->get("food");
	$w = $this->settings->get("weapon");
	
	$inv->setHelmet($h[0],$h[1],$h[2]);
	$inv->setChestplate($c[0],$c[1],$c[2]);
	$inv->setLeggings($l[0],$l[1],$l[2]);
	$inv->setBoots($b[0],$b[1],$b[2]);
	$inv->sendContents($player);
	$inv->sendArmorContents($player);
}
public function sendMessageType(Player $player,$message){
	if($this->settings->get("message-type") === "popup"){
		$player->sendPopup($this->translateColors("&",$message));
	}else{
		$player->sendTip($this->translateColors("&",$message));
	}
	
}
public function translateColors($symbol = "&", $message){
    
    	$message = str_replace($symbol."0", TextFormat::BLACK, $message);
    	$message = str_replace($symbol."1", TextFormat::DARK_BLUE, $message);
    	$message = str_replace($symbol."2", TextFormat::DARK_GREEN, $message);
    	$message = str_replace($symbol."3", TextFormat::DARK_AQUA, $message);
    	$message = str_replace($symbol."4", TextFormat::DARK_RED, $message);
    	$message = str_replace($symbol."5", TextFormat::DARK_PURPLE, $message);
    	$message = str_replace($symbol."6", TextFormat::GOLD, $message);
    	$message = str_replace($symbol."7", TextFormat::GRAY, $message);
    	$message = str_replace($symbol."8", TextFormat::DARK_GRAY, $message);
    	$message = str_replace($symbol."9", TextFormat::BLUE, $message);
    	$message = str_replace($symbol."a", TextFormat::GREEN, $message);
    	$message = str_replace($symbol."b", TextFormat::AQUA, $message);
    	$message = str_replace($symbol."c", TextFormat::RED, $message);
    	$message = str_replace($symbol."d", TextFormat::LIGHT_PURPLE, $message);
    	$message = str_replace($symbol."e", TextFormat::YELLOW, $message);
    	$message = str_replace($symbol."f", TextFormat::WHITE, $message);
    
    	$message = str_replace($symbol."k", TextFormat::OBFUSCATED, $message);
    	$message = str_replace($symbol."l", TextFormat::BOLD, $message);
    	$message = str_replace($symbol."m", TextFormat::STRIKETHROUGH, $message);
    	$message = str_replace($symbol."n", TextFormat::UNDERLINE, $message);
    	$message = str_replace($symbol."o", TextFormat::ITALIC, $message);
    	$message = str_replace($symbol."r", TextFormat::RESET, $message);
    
    	return $message;
    }
public function resetSign($map,$int){
	$signconfig = $this->settings->get("sign[{$map}]");
	$this->sign = new Vector3($signconfig["x"],$signconfig["y"],$signconfig["z"]);
	if($this->sign instanceof \pocketmine\tile\Sign){
		if($int === "ready"){
		$signready = $this->settings->get("sign-ready");
		$sign->setText($signready[0],$signready[1],$signready[2],$signready[3]);
		}
		if($int === "queue"){
		$signqueue = $this->settings->get("sign-queuing");
		$sign->setText($signqueue[0],$signqueue[1],$signqueue[2],$signqueue[3]);
		}
		if($int === "running"){
		$signrunning = $this->settings->get("sign-running");
		$sign->setText($signrunning[0],$signrunning[1],$signrunning[2],$signrunning[3]);
		}
	}
}
public function pickTeam($map,Player $player){
	if(count($this->bluePlayers[$map]) and count($this->redPlayers[$map]) === 10){
		return false;
	}
	if(count($this->bluePlayers[$map]) < count($this->redPlayers[$map])){
		$this->bluePlayers[$map][$player->getName()] = array("Player" => $player->getName());
	}else{
		$this->redPlayers[$map][$player->getName()] = array("Player" => $player->getName());
	}
}
public function stopMatch($map){
	$this->maps[$map] = $this->settings->get("playTime");
	$this->resetSign($map,"ready");
	foreach($this->getServer()->getLevelByName($map)->getPlayers() as $player){
		$player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
	}
}
public function checkScore($map){
	if(!in_array($map, $this->score)){
		return;
	}
	if($this->score[$map]["BlueTeam"] === $this->settings->get("score-to-win")){
		$this->stopMatch($map);
	}
	if($this->score[$map]["RedTeam"] === $this->settings->get("score-to-win")){
		$this->stopMatch($map);
	}
}
public function winMatch($map,Player $player){
	$message = str_replace("{PLAYER}", $player->getName(), $this->settings->get("win-message"));
	$this->getServer()->broadcastMessage($this->translateColors("&",$message));
}
public function nextPoint($map,Player $player){
	if(isset($this->bluePlayers[$map][$player->getName()])){
		$this->score[$map]["BlueTeam"]++;
	}
	if(isset($this->redPlayers[$map][$player->getName()])){
		$this->score[$map]["RedTeam"]++;
	}
}
public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		$this->commands->onCommand($sender, $command, $label, $args);
	}
public function runMatches(){
	foreach($this->areans->get("Maps") as $m){
		$level = $this->getServer()->getLevelByName($m);
		if($level !== null){
                    $this->maps[$m]--;
			foreach($level->getPlayers() as $p){
				if($this->maps[$m] === 0){
					$this->stopMatch($m);
				}
			$this->sendMessageType($p,$this->maps[$m]);
				
			}
		}
	}
}
public function setTime($m,$time){
    $this->maps[$m] = $this->time[$time];
    return $time;
}
}

