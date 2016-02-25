<?php

/**
 * Class UBEFleetUnitList
 *
 * @method UBEFleetUnit offsetGet($offset)
 * @property UBEFleetUnit[] $_container
 */
class UBEFleetUnitList extends ArrayAccessV2 {

  public function __clone() {
    parent::__clone(); // TODO: Change the autogenerated stub
  }

  // TODO - читать инфу здесь?
  public function insert_unit($unit_id, $unit_count = 0) {
    if(!isset($this[$unit_id])) {
      $this[$unit_id] = new UBEFleetUnit();
      $this[$unit_id]->unit_id = $unit_id;
      $this[$unit_id]->count = $unit_count;
    }
  }

  public function get_units_count() {
    $result = 0;
    foreach($this->_container as $UBEFleetUnit) {
      $result += $UBEFleetUnit->count;
    }
    return $result;
  }

  public function get_units_lost() {
    $result = 0;
    foreach($this->_container as $UBEFleetUnit) {
      $result += $UBEFleetUnit->units_lost; // Если будет сделано восстановлении более, чем начальное число - тут надо сделать сумму по модулю
    }
    return $result;
  }


  /**
   * @param $bonus_list
   */
  public function fill_unit_info($bonus_list) {
    foreach($this->_container as $UBEFleetUnit) {
      $UBEFleetUnit->fill_unit_info($bonus_list);
    }
  }


  public function analyze_fleet_units() {

  }
}
