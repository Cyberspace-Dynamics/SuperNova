<?php

include('common.' . substr(strrchr(__FILE__, '.'), 1));

if(sys_get_param_int('BE_DEBUG') && !defined('BE_DEBUG'))
{
  define('BE_DEBUG', true);
}

require_once('includes/includes/coe_simulator_helpers.php');

$replay = $_GET['replay'] ? $_GET['replay'] : $_POST['replay'];
$execute = intval($_GET['execute']);
$sym_defender = $_POST['defender'] ? $_POST['defender'] : array();
$sym_attacker = $_POST['attacker'] ? $_POST['attacker'] : array();

if($replay)
{
  $unpacked = sn_ube_simulator_decode_replay($replay);

  $sym_defender = $unpacked['D'];
  $sym_attacker = $unpacked['A'];
}
else
{
  $sym_defender = array(0 => $sym_defender);
  $sym_attacker = array(1 => $sym_attacker);
}

if($_POST['submit'] || $execute)
{
  $replay = sn_ube_simulator_encode_replay($sym_defender, 'D');
  $replay .= sn_ube_simulator_encode_replay($sym_attacker, 'A');

  require_once('classes/UBE/UBE.php');
  UBE::display_simulator($sym_attacker, $sym_defender);
}
else
{
  $template = gettemplate('simulator', true);
  $techs_and_officers = array(TECH_WEAPON, TECH_SHIELD, TECH_ARMOR, MRC_ADMIRAL);

  foreach($techs_and_officers as $tech_id)
  {
    if(!$sym_attacker[1][$tech_id])
    {
      $sym_attacker[1][$tech_id] = mrc_get_level($user, null, $tech_id);
    }
  }

  $show_groups = array(
    UNIT_TECHNOLOGIES => array(TECH_WEAPON, TECH_SHIELD, TECH_ARMOR),
    UNIT_MERCENARIES => array(MRC_ADMIRAL),
    UNIT_SHIPS => Fleet::$snGroupFleet,
    UNIT_RESOURCES => sn_get_groups('resources_loot'),
    UNIT_GOVERNORS => array(MRC_FORTIFIER),
    UNIT_DEFENCE => sn_get_groups('defense_active'),
  );
  foreach($show_groups as $unit_group_id => $unit_group)
  {
    $template->assign_block_vars('simulator', array(
      'GROUP' => $unit_group_id,
      'NAME' => classLocale::$lang['tech'][$unit_group_id],
    ));

    foreach($unit_group as $unit_id)
    {
      $tab++;

      $value = mrc_get_level($user, $planetrow, $unit_id);

      $template->assign_block_vars('simulator', array(
        'NUM'      => $tab < 9 ? "0{$tab}" : $tab,
        'ID'       => $unit_id,
        'GROUP'    => $unit_group_id,
        'NAME'     => classLocale::$lang['tech'][$unit_id],
        'ATTACKER' => intval($sym_attacker[1][$unit_id]),
        'DEFENDER' => intval($sym_defender[0][$unit_id]),
        'VALUE'    => $value,
      ));
    }
  }

  $template->assign_vars(array(
    'BE_DEBUG' => BE_DEBUG,
    'UNIT_DEFENCE' => UNIT_DEFENCE,
    'UNIT_GOVERNORS' => UNIT_GOVERNORS,
  ));

  display($template, classLocale::$lang['coe_combatSimulator'], false);
}
