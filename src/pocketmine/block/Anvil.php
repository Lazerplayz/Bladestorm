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

namespace pocketmine\block;

use pocketmine\inventory\AnvilInventory;
use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;
use pocketmine\Player;

class Anvil extends Fallable{

	const TYPE_NORMAL = 0;
	const TYPE_SLIGHTLY_DAMAGED = 4;
	const TYPE_VERY_DAMAGED = 8;

	protected $id = Block::ANVIL;

	public function isSolid() : bool{
		return false;
	}

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 5;
	}

	public function getBlastResistance() : float{
		return 6000;
	}

	public function getName() : string{
		static $names = [
			self::TYPE_NORMAL => "Anvil",
			self::TYPE_SLIGHTLY_DAMAGED => "Slightly Damaged Anvil",
			self::TYPE_VERY_DAMAGED => "Very Damaged Anvil"
		];
		return $names[$this->meta & 0x0c] ?? "Anvil";
	}

	public function getVariantBitmask() : int{
		return 0x0c;
	}

	public function getToolType() : int{
		return Tool::TYPE_PICKAXE;
	}

	public function getRequiredHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}

	public function onActivate(Item $item, Player $player = null) : bool{
		if($player instanceof Player){
			$player->addWindow(new AnvilInventory($this));
		}

		return true;
	}

	public function place(Item $item, Block $block, Block $target, int $face, float $fx, float $fy, float $fz, Player $player = null) : bool{
		$direction = ($player !== null ? $player->getDirection() : 0) & 0x03;
		$this->meta = ($this->meta << 2) | $direction;
		return $this->getLevel()->setBlock($block, $this, true, true);
	}

	public function getDrops(Item $item) : array{
		if($this->canBeBrokenWith($item)){
			return [
				Item::get(Item::ANVIL, $this->meta >> 2, 1)
			];
		}

		return [];
	}
}