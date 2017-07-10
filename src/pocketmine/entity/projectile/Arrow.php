<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\entity\projectile;

use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class Arrow extends Projectile{
	const NETWORK_ID = 80;

	public $width = 0.5;
	public $length = 0.5;
	public $height = 0.5;

	protected $gravity = 0.05;
	protected $drag = 0.01;

	protected $damage = 2;

	public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null, bool $critical = false){
		parent::__construct($level, $nbt, $shootingEntity);
		$this->setCritical($critical);
	}

	public function isCritical() : bool{
		return $this->getDataFlag(self::DATA_FLAGS, self::DATA_FLAG_CRITICAL);
	}

	public function setCritical(bool $value = true){
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_CRITICAL, $value);
	}

	public function getResultDamage() : int{
		$base = parent::getResultDamage();
		if($this->isCritical()){
			return ($base + mt_rand(0, (int) ($base / 2) + 1));
		}else{
			return $base;
		}
	}

	public function onUpdate($currentTick){
		if($this->closed){
			return false;
		}

		$this->timings->startTiming();

		$hasUpdate = parent::onUpdate($currentTick);

		if($this->onGround or $this->hadCollision){
			$this->setCritical(false);
		}

		if($this->age > 1200){
			$this->close();
			$hasUpdate = true;
		}

		$this->timings->stopTiming();

		return $hasUpdate;
	}
}