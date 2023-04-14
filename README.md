# ClanAPI

ClanAPI is a virion for PocketMine-MP that provides basic functionality for managing clans 
###### Open an issue if you have errors or dm me on discord Inaa#0001

## Usage

To use the ClanAPI, you can create a new instance of the ClanAPI class:
```php
use Inaayat\Clan\ClanAPI;
// MySQL
$clanAPI = new ClanAPI($ip, $username, $password, $database, $port);
```
You can then use the methods provided by the ClanAPI class to perform various operations on clans:

<h2>Creating and Deleting Clans</h2>
<ul>
  <li><code>$clanAPI->createTable()</code>: creates the necessary table in the database</li>
  <li><code>$clanAPI->addClan($name, $leader)</code>: adds a new clan to the database</li>
  <li><code>$clanAPI->deleteClan($name)</code>: deletes a clan from the database</li>
</ul>

<h2>Managing Clan Members</h2>
<ul>
  <li><code>$clanAPI->addPlayerToClan($clan, $player)</code>: adds a player to a clan</li>
  <li><code>$clanAPI->removePlayerFromClan($clan, $player)</code>: removes a player from a clan</li>
  <li><code>$clanAPI->getClanByPlayer($player)</code>: gets the name of the clan that a player belongs to</li>
  <li><code>$clanAPI->isLeader($player, $clan)</code>: checks if a player is the leader of a clan</li>
  <li><code>$clanAPI->getAllPlayersInClan($clanName)</code>: gets an array of all players in a clan</li>
  <li><code>$clanAPI->getPlayerCountInClan($clanName)</code>: gets the number of players in a clan</li>
  <li><code>$clanAPI->isInClan($player)</code>: checks if a player is in a clan</li>
</ul>

<h2>Getting Clan Information</h2>
<ul>
  <li><code>$clanAPI->getClan($name)</code>: gets information about a clan</li>
</ul>

<h2>Managing Clan Properties</h2>
<ul>
  <li><code>$clanAPI->setClanDescription($clan, $description)</code>: sets the description of a clan</li>
  <li><code>$clanAPI->addClanPoints($clan, $points)</code>: adds points to a clan</li>
  <li><code>$clanAPI->removeClanPoints($clan, $points)</code>: removes points from a clan</li>
  <li><code>$clanAPI->setClanPoints($clan, $points)</code>: sets the points of a clan</li>
  <li><code>$clanAPI->isClanOpen($name)</code>: checks if a clan is open</li>
  <li><code>$clanAPI->isClanClosed($name)</code>: checks if a clan is closed</li>
  <li><code>$clanAPI->setClanStatus($name, $status)</code>: sets the status of a clan</li>
  <li><code>$clanAPI->getClanDescription($name)</code>: gets the clan description</li>
  <li><code>$clanAPI->getClanPoints($name)</code>: gets the clan points</li>
</ul>
