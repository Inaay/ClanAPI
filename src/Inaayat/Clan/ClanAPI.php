<?php

namespace Inaayat\Clan;

use pocketmine\player\Player;
use mysqli;

class ClanAPI {

	private $connection;

	public function __construct($ip, $username, $password, $database, $port){
		$this->connection = new mysqli($ip, $username, $password, $database, $port);
		if ($this->connection->connect_error) {
			die("Connection failed: " . $this->connection->connect_error);
		}
	}

	public function getConnection(){
		return $this->connection;
	}

	public function createTable(){
		$sql = "CREATE TABLE IF NOT EXISTS clans (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(30) NOT NULL,
        leader VARCHAR(30) NOT NULL,
        members TEXT NOT NULL,
        description TEXT NOT NULL,
        points INT(11) NOT NULL,
        status VARCHAR(10) NOT NULL
    )";
		if ($this->connection->query($sql) === false) {
			throw new ClanException("Error creating table: " . $this->connection->error);
		}
	}

	public function addClan($name, $leader){
		$members = json_encode([$leader]);
		$description = "No clan description :(";
		$points = 0;
		$status = "open";
		$stmt = $this->connection->prepare("INSERT INTO clans (name, leader, members, description, points, status) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssis", $name, $leader, $members, $description, $points, $status);
		if ($stmt->execute() === false) {
			throw new ClanException("Error adding clan: " . $stmt->error);
		}
		$stmt->close();
	}

	public function deleteClan($name){
		$stmt = $this->connection->prepare("DELETE FROM clans WHERE name = ?");
		$stmt->bind_param("s", $name);
		if ($stmt->execute() === false) {
			throw new ClanException("Error deleting clan: " . $stmt->error);
		}
		$stmt->close();
	}

	public function addPlayerToClan($clan, $player){
		$stmt = $this->connection->prepare("SELECT members FROM clans WHERE name = ?");
		$stmt->bind_param("s", $clan);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$members = json_decode($row["members"]);
		if (!in_array($player, $members)) {
			array_push($members, $player);
			$members = json_encode($members);
			$stmt = $this->connection->prepare("UPDATE clans SET members = ? WHERE name = ?");
			$stmt->bind_param("ss", $members, $clan);
			if ($stmt->execute() === false) {
				throw new ClanException("Error adding player to clan: " . $stmt->error);
			}
			$stmt->close();
		}
	}

	public function removePlayerFromClan($clan, $player){
		$stmt = $this->connection->prepare("SELECT members FROM clans WHERE name = ?");
		$stmt->bind_param("s", $clan);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$members = json_decode($row["members"]);
		if (in_array($player, $members)) {
			$index = array_search($player, $members);
			unset($members[$index]);
			$members = json_encode($members);
			$stmt = $this->connection->prepare("UPDATE clans SET members = ? WHERE name = ?");
			$stmt->bind_param("ss", $members, $clan);
			if ($stmt->execute() === false) {
				throw new ClanException("Error removing player from clan: " . $stmt->error);
			}
			$stmt->close();
		}
	}

	public function getClan($name){
		$stmt = $this->connection->prepare("SELECT * FROM clans WHERE name = ?");
		$stmt->bind_param("s", $name);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$clan = [
			"leader" => $row["leader"],
			"members" => json_decode($row["members"]),
			"description" => $row["description"],
			"points" => $row["points"],
			"status" => $row["status"]
		];
		$stmt->close();
		return $clan;
	}

	public function getClanByPlayer(Player $player){
		$playerName = $player->getName();
		$stmt = $this->connection->prepare("SELECT name FROM clans WHERE members LIKE ?");
		$stmt->bind_param("s", "%$playerName%");
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$stmt->close();
		return $row ? $row["name"] : null;
	}

	public function isLeader(Player $player, string $clan): bool{
		$name = $player->getName();
		$stmt = $this->connection->prepare("SELECT leader FROM clans WHERE name = ?");
		$stmt->bind_param("s", $clan);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$stmt->close();
		return $row && $row["leader"] === $name;
	}

	public function getAllPlayersInClan(string $clanName): array{
		$stmt = $this->connection->prepare("SELECT members FROM clans WHERE name = ?");
		$stmt->bind_param("s", $clanName);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$stmt->close();
		return $row ? json_decode($row["members"]) : [];
	}

	public function getPlayerCountInClan(string $clanName): int{
		$stmt = $this->connection->prepare("SELECT members FROM clans WHERE name = ?");
		$stmt->bind_param("s", $clanName);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$stmt->close();
		return $row ? count(json_decode($row["members"])) : 0;
	}

	public function isInClan(Player $player): bool{
		$playerName = $player->getName();
		$stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM clans WHERE members LIKE ?");
		$stmt->bind_param("s", "%$playerName%");
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$stmt->close();
		return $row && $row["count"] > 0;
	}

	public function setClanDescription($clan, $description){
		$stmt = $this->connection->prepare("UPDATE clans SET description = ? WHERE name = ?");
		$stmt->bind_param("ss", $description, $clan);
		if ($stmt->execute() === false) {
			throw new ClanException("Error setting clan description: " . $stmt->error);
		}
		$stmt->close();
	}

	public function addClanPoints($clan, $points){
		$stmt = $this->connection->prepare("UPDATE clans SET points = points + ? WHERE name = ?");
		$stmt->bind_param("is", $points, $clan);
		if ($stmt->execute() === false) {
			throw new ClanException("Error adding clan points: " . $stmt->error);
		}
		$stmt->close();
	}

	public function removeClanPoints($clan, $points){
		$stmt = $this->connection->prepare("UPDATE clans SET points = points - ? WHERE name = ?");
		$stmt->bind_param("is", $points, $clan);
		if ($stmt->execute() === false) {
			throw new ClanException("Error removing clan points: " . $stmt->error);
		}
		$stmt->close();
	}

	public function setClanPoints($clan, $points){
		$stmt = $this->connection->prepare("UPDATE clans SET points = ? WHERE name = ?");
		$stmt->bind_param("is", $points, $clan);
		if ($stmt->execute() === false) {
			throw new ClanException("Error setting clan points: " . $stmt->error);
		}
		$stmt->close();
	}

	public function isClanOpen($name){
		$stmt = $this->connection->prepare("SELECT status FROM clans WHERE name = ?");
		$stmt->bind_param("s", $name);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$stmt->close();
		return $row["status"] === "open";
	}

	public function isClanClosed($name){
		$stmt = $this->connection->prepare("SELECT status FROM clans WHERE name = ?");
		$stmt->bind_param("s", $name);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$stmt->close();
		return $row["status"] === "closed";
	}

	public function setClanStatus($name, $status){
		$stmt = $this->connection->prepare("UPDATE clans SET status = ? WHERE name = ?");
		$stmt->bind_param("ss", $status, $name);
		if ($stmt->execute() === false) {
			throw new ClanException("Error setting clan status: " . $stmt->error);
		}
		$stmt->close();
	}
}