<?php
namespace BrawlTDM\tasks;
use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;
use pocketmine\Player;
use BrawlTDM\Main;
class scorefetcher extends PluginTask{
private $plugin;
	
	public function __construct(Main $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
		$this->settings = $this->plugin->settings;
		$this->map = $this->plugin->map;
		$this->areans = $this->plugin->areans;
	}
	public function onRun($tick){
	foreach($this->areans->get("Maps") as $m){
	$this->plugin->checkScore($m);	
	}
	$this->plugin->runMatches();
}
}
