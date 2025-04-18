<?php

switch ($action) {
  case 0: // Default -- Spell menu
    $body = new Template("templates/spells/spells.default.tmpl.php");
    break;
  case 1: // Search spells
    $body = new Template("templates/spells/spells.searchresults.tmpl.php");
    if (isset($_GET['id']) && $_GET['id'] != "ID") {
      $results = search_spell_by_id();
    }
    else $results = search_spells_by_name();
    $body->set("results", $results);
    break;
  case 2: // Edit spell
    $javascript = new Template("templates/iframes/js.tmpl.php");
    $body = new Template("templates/spells/spells.edit.tmpl.php");
    $body->set("formulas", $sp_formulas);
    $body->set("buffformulas", $sp_buffformulas);
    $body->set("zonetypes", $sp_zonetypes);
    $body->set("envtypes", $sp_envtypes);
    $body->set("classnums", $sp_classnums);
    $body->set("deities", $sp_deities);
    $body->set("targets", $sp_targets);
    $body->set("npccats", $sp_npccats);
    $body->set("spellgroups", $sp_spellgroups);
    $body->set("skills", $sp_skills);
    $body->set("spelltypes", $sp_spelltypes);
    $body->set("effecttypes", $sp_goodeffect);
    $body->set("resisttypes", $sp_resisttype);
    $body->set("effects", $sp_effects);
    $body->set("lighttypes", $sp_lighttypes);
    $body->set("acttypes", $sp_acttypes);
    $body->set("daytimes", $sp_daytimes);
    $body->set("traveltypes", $sp_traveltypes);
    //$body->set("spellcats", $sp_categories); //Wtf is this?
	$body->set("castanims", $sp_animationlist);
	$body->set("restrictions", $sp_restrictions);
	$body->set("targetanims", $sp_animationlist);

    $vars = spell_info();
    if ($vars) {
      foreach ($vars as $key=>$value) {
        $body->set($key, $value);
      }
    }

    $vars = getdate();
    if ($vars) {
      foreach ($vars as $key=>$value) {
        $body->set($key, $value);
      }
    }
	
	$ass = spell_dbstrus();
	if ($ass) {
       $body->set("txtfile", $ass);
	} else {
		$body->set("txtfile", "");
	}

    break;
  case 5: // Delete spell
      check_authorization();
      delete_spell();
      header("Location: index.php?editor=spells");
      exit;
  case 6: // Update spell
     check_authorization();
     $id = $_GET['id'];
     update_spell();
	 update_dbstrus();
     header("Location: index.php?editor=spells&id=$id&action=2");
     exit;
  case 7: // Copy spell
      check_authorization();
      $id = copy_spell();
      header("Location: index.php?editor=spells&id=$id&action=2");
      exit;
  case 999: // Dump dbstr_us
	  check_authorization();
	  $body = new Template("templates/spells/spells.gendbstrus.tmpl.php");
	  $response = generateDBSTRUSFile();
	  $body->set('response', $response);
	 break;
  case 9999: // Import dbstr_us
	  check_authorization();
	  $body = new Template("templates/spells/spells.importdbstrus.tmpl.php");
	  $response = importDBSTRUSFile();
	  $body->set('response', $response);
	 break;
//Spells are complicated enough that one is probably wise to copy from a template anyway, at least for now.
//  case 8: // Add spell
//     check_authorization();
//     $javascript = new Template("templates/iframes/js.tmpl.php");
//     $body = new Template("templates/spells/spells.add.tmpl.php");
//     break;
//  case 9: //Add spell
//     check_authorization();
//     add_spell();
//     $id = $_POST['id'];
//     header("Location: index.php?editor=spells&id=$id&action=2");
//     exit;
	// Natedog Custom shit.. OFF for now
	
   //case 12: // Dump SkillCaps
	// check_authorization();
	// $body = new Template("templates/spells/genskillcaps.php");
	// break;
  case 10: // Dump spells
    check_authorization();
    $body = new Template("templates/spells/spells.genspellsfile.tmpl.php");
    $response = generateSpellsFile();
    $body->set('response', $response);
    break;
  case 11: // NPC Spells Effects List
    $body = new Template("templates/spells/npc.spells.effects.default.tmpl.php");
    $breadcrumbs = $breadcrumbs . " >> NPC Spells Effects";
    $effects = npc_spells_effects();
    if ($effects)
      $body->set('effects', $effects);
    break;
  case 12: // View NPC Spells Effect
    $body = new Template("templates/spells/npc.spells.effects.view.tmpl.php");
    $effect = npc_spells_effect($_GET['nseid']);
    if ($effect)
      $body->set('effect', $effect);
    $entries = npc_spells_effects_entries($_GET['nseid']);
    if ($entries)
      $body->set('entries', $entries);
    $body->set('sp_effects', $sp_effects);
    break;
  case 13: // Add NPC Spells Effects
    check_authorization();
    $body = new Template("templates/spells/npc.spells.effect.add.tmpl.php");
    $id = npc_spells_effects_next_id();
    $body->set('id', $id);
    break;
  case 14: // Insert NPC Spells Effects
    check_authorization();
    insert_npc_spells_effect();
    $previous = $_POST['id'];
    header("Location: index.php?editor=spells&action=12&nseid=$previous");
    exit;
  case 15: // Edit NPC Spells Effects
    check_authorization();
    $body = new Template("templates/spells/npc.spells.effect.edit.tmpl.php");
    $effect = npc_spells_effect($_GET['nseid']);
    if ($effect) {
      $body->set('effect', $effect);
    }
    break;
  case 16: // Update NPC Spells Effects
    check_authorization();
    update_npc_spells_effect();
    $previous = $_POST['id'];
    header("Location: index.php?editor=spells&action=12&nseid=$previous");
    exit;
  case 17: // Delete NPC Spells Effects
    check_authorization();
    delete_npc_spells_effect();
    header("Location: index.php?editor=spells&action=11");
    exit;
  case 18: // Add NPC Spells Effects Entry
    check_authorization();
    $body = new Template("templates/spells/npc.spells.effects.entry.add.tmpl.php");
    $id = npc_spells_effects_entry_next_id();
    $nseid = $_GET['nseid'];
    $current = current_npc_spells_effects($nseid);
    $body->set('id', $id);
    $body->set('npc_spells_effects_id', $nseid);
    if ($current)
      $body->set('current', $current);
    $body->set('sp_effects', $sp_effects);
    break;
  case 19: // Insert NPC Spells Effects Entry
    check_authorization();
    insert_npc_spells_effects_entry();
    $previous = $_POST['npc_spells_effects_id'];
    header("Location: index.php?editor=spells&action=12&nseid=$previous");
    exit;
  case 20: // Edit NPC Spells Effects Entry
    check_authorization();
    $body = new Template("templates/spells/npc.spells.effects.entry.edit.tmpl.php");
    $entry = npc_spells_effects_entry($_GET['nseeid']);
    $current = current_npc_spells_effects($nseid);
    if ($entry) {
      foreach ($entry as $key=>$value) {
        $body->set($key, $value);
      }
    }
    if ($current)
      $body->set('current', $current);
    $body->set('sp_effects', $sp_effects);
    break;
  case 21: // Update NPC Spells Effects Entry
    check_authorization();
    update_npc_spells_effects_entry();
    $previous = $_GET['nseid'];
    header("Location: index.php?editor=spells&action=12&nseid=$previous");
    exit;
  case 22: // Delete NPC Spells Effects Entry
    check_authorization();
    delete_npc_spells_effects_entry();
    $previous = $_GET['nseid'];
    header("Location: index.php?editor=spells&action=12&nseid=$previous");
    exit;
}

function spell_info() {
  global $mysql_content_db;

  $id = $_GET['id'];
  
  $query = "SELECT * FROM spells_new WHERE id=$id";
  $result = $mysql_content_db->query_assoc($query);

  return $result;
}

function spell_dbstrus() {
  global $mysql_content_db;

  $id = $_GET['id'];
  //$descnum = $_POST['descnum'];
  $query1 = "SELECT descnum FROM spells_new WHERE id = $id";
  $result1 = $mysql_content_db->query_assoc($query1);
  $descnum = $result1['descnum'];

  $query2 = "SELECT value FROM db_str WHERE `id`=$descnum and `type`=6";
  $result2 = $mysql_content_db->query_assoc($query2);
  if(isset($result2['value'])) {
	$txtfile = $result2['value'];
  } else {
	  $txtfile = "";
  }

  return $txtfile;
}

function update_dbstrus() {
	global $mysql_content_db;
	
	$descnum = $_POST['descnum'];
	$txtfile = mysqli_real_escape_string($mysql_content_db, $_POST['txtfile']);
	if ($descnum > 0) {
		$query = "REPLACE INTO db_str VALUES ($descnum, 6, \"$txtfile\")";
		$mysql_content_db->query_no_result($query);
	}
}

function delete_spell() {
  global $mysql_content_db;

  $id = $_GET['id'];

  $query = "DELETE FROM spells_new WHERE id=$id";
  $mysql_content_db->query_no_result($query);
}

function update_spell() {
  global $mysql_content_db;

  $id = $_POST['id'];
  $vars = spell_info();

  //Checkbox list
  $cbs = array(
    "dot_stacking_exempt",
    "deleteable",
    "uninterruptable",
    "nodispell",
    "can_mgb",
    "short_buff_box",
    "deities0",
    "deities1", 
    "deities2",
    "deities3",
    "deities4",
    "deities5",
    "deities6",
    "deities7",
    "deities8",
    "deities9",
    "deities10",
    "deities11",
    "deities12",
    "deities13",
    "deities14",
    "deities15",
    "deities16"
  );
  
  //Sanitize checkboxes
  foreach ($cbs as $cb) {
    if (isset($_POST[$cb])){
      $_POST[$cb] = 1;
    }
    else {
      $_POST[$cb] = 0;
    }
  }

  //Fix the 'use text field' elements
  if ($_POST['spell_category'] == -100) {
    $_POST['spell_category'] = $_POST[spcat];
  }
  
    //Fix the 'use text field' elements
  if ($_POST['field220'] == -100) {
    $_POST['field220'] = $_POST[f220text];
  }
  
  if ($_POST['CastRestriction'] == -100) {
    $_POST['CastRestriction'] = $_POST[CastRestrictionText];
  }
  
   //Fix the 'use text field' elements
 // if ($_POST['field220'] >= 10000 && $_POST['field220'] <= 11000) {
 //   $_POST['field220'] = $_POST[f220text];
 // }
  

  $fields = '';
  foreach(array_keys($vars) as $f) {
    //Put field name in backticks to avoid conflicts with columns named for sql functions (like range)
    if(isset($_POST[$f]) && ($vars[$f] != stripslashes($_POST[$f]))) {
      $fields .= "`$f` = \"$_POST[$f]\", ";
    }
  }
  $fields =  rtrim($fields, ", ");

  if ($fields != '') {
    $query = "UPDATE spells_new SET $fields WHERE id=$id";
    $mysql_content_db->query_no_result($query);
  }
}

function copy_spell() {
  global $mysql_content_db, $sp_fields;
  
  $next_id_array = array();
  $newid = 0;
  $id = $_GET['id'];
  
  $query1 = "SELECT id FROM spells_new";
  $result = $mysql_content_db->query($query1);
  
  while ($z = $result->fetch_assoc()) {
		$next_id_array[$z['id']] = 1;
  }
  
  for ($x = 3; $x <= 50000; $x++) {
		if ($next_id_array[$x] != 1) {
			$newid = $x;
			break;
		}
  }
  //$query1 = "SELECT max(id) AS iid FROM spells_new";
  //$result = $mysql_content_db->query_assoc($query1);
  //$newid = $result['iid'] + 1;
  //
  $fields = implode(", ", $sp_fields);
  
  $query2 = "INSERT INTO spells_new ($fields, id) SELECT $fields, $newid AS id FROM spells_new WHERE id = '$id'";
  $mysql_content_db->query_no_result($query2);
  
  $query3 = "UPDATE spells_new SET name = concat(name, ' - Copy') WHERE id = $newid";
  $mysql_content_db->query_no_result($query3);

  return $newid;
}

function get_max_id() {
  global $mysql_content_db;

  $query = "SELECT max(id) AS iid FROM spells_new";
  $result = $mysql_content_db->query_assoc($query);
  $newid = $result['iid'] + 1;

  return $newid;
}

function npc_spells_effects() {
  global $mysql_content_db;

  $query = "SELECT * FROM npc_spells_effects ORDER BY id";
  $result = $mysql_content_db->query_mult_assoc($query);

  if ($result) {
    return $result;
  }
  else {
    return null;
  }
}

function npc_spells_effect($nseid) {
  global $mysql_content_db;

  $query = "SELECT * FROM npc_spells_effects WHERE id=$nseid";
  $result = $mysql_content_db->query_assoc($query);

  if ($result) {
    return $result;
  }
  else {
    return null;
  }
}

function npc_spells_effects_entries($nseid) {
  global $mysql_content_db;

  $query = "SELECT * FROM npc_spells_effects_entries WHERE npc_spells_effects_id=$nseid ORDER BY spell_effect_id";
  $results = $mysql_content_db->query_mult_assoc($query);

  if ($results) {
    return $results;
  }
  else {
    return null;
  }
}

function npc_spells_effects_entry($nseeid) {
  global $mysql_content_db;

  $query = "SELECT * FROM npc_spells_effects_entries WHERE id=$nseeid";
  $results = $mysql_content_db->query_assoc($query);

  if ($results) {
    return $results;
  }
  else {
    return null;
  }
}

function npc_spells_effects_next_id() {
  global $mysql_content_db;

  $query = "SELECT MAX(id) AS last FROM npc_spells_effects";
  $result = $mysql_content_db->query_assoc($query);

  return $result['last'] + 1;
}

function npc_spells_effects_entry_next_id() {
  global $mysql_content_db;

  $query = "SELECT MAX(id) AS last FROM npc_spells_effects_entries";
  $result = $mysql_content_db->query_assoc($query);

  return $result['last'] + 1;
}

function insert_npc_spells_effect() {
  global $mysql_content_db;

  $id = $_POST['id'];
  $name = $_POST['name'];
  $parent_list = $_POST['parent_list'];

  $query = "INSERT INTO npc_spells_effects (id, name, parent_list) VALUES ($id, '$name', $parent_list)";
  $mysql_content_db->query_no_result($query);
}

function update_npc_spells_effect() {
  global $mysql_content_db;

  $id = $_POST['id'];
  $name = $_POST['name'];
  $parent_list = $_POST['parent_list'];

  $query = "UPDATE npc_spells_effects SET name='$name', parent_list=$parent_list WHERE id=$id";
  $mysql_content_db->query_no_result($query);
}

function delete_npc_spells_effect() {
  global $mysql_content_db;

  $nseid = $_GET['nseid'];

  $query1 = "DELETE FROM npc_spells_effects_entries WHERE npc_spells_effects_id=$nseid";
  $mysql_content_db->query_no_result($query1);

  $query2 = "DELETE FROM npc_spells_effects WHERE id=$nseid";
  $mysql_content_db->query_no_result($query2);
}

function insert_npc_spells_effects_entry() {
  global $mysql_content_db;

  $id = $_POST['id'];
  $npc_spells_effects_id = $_POST['npc_spells_effects_id'];
  $spell_effect_id = $_POST['spell_effect_id'];
  $minlevel = $_POST['minlevel'];
  $maxlevel = $_POST['maxlevel'];
  $se_base = $_POST['se_base'];
  $se_limit = $_POST['se_limit'];
  $se_max = $_POST['se_max'];

  $query = "INSERT INTO npc_spells_effects_entries (id, npc_spells_effects_id, spell_effect_id, minlevel, maxlevel, se_base, se_limit, se_max) VALUES ($id, $npc_spells_effects_id, $spell_effect_id, $minlevel, $maxlevel, $se_base, $se_limit, $se_max)";
  $mysql_content_db->query_no_result($query);
}

function update_npc_spells_effects_entry() {
  global $mysql_content_db;

  $id = $_POST['id'];
  $npc_spells_effects_id = $_POST['npc_spells_effects_id'];
  $spell_effect_id = $_POST['spell_effect_id'];
  $minlevel = $_POST['minlevel'];
  $maxlevel = $_POST['maxlevel'];
  $se_base = $_POST['se_base'];
  $se_limit = $_POST['se_limit'];
  $se_max = $_POST['se_max'];

  $query = "UPDATE npc_spells_effects_entries SET spell_effect_id=$spell_effect_id, minlevel=$minlevel, maxlevel=$maxlevel, se_base=$se_base, se_limit=$se_limit, se_max=$se_max WHERE id=$id AND npc_spells_effects_id=$npc_spells_effects_id";
  $mysql_content_db->query_no_result($query);
}

function delete_npc_spells_effects_entry() {
  global $mysql_content_db;

  $nseeid = $_GET['nseeid'];
  $nseid = $_GET['nseid'];

  $query = "DELETE FROM npc_spells_effects_entries WHERE id=$nseeid AND npc_spells_effects_id=$nseid";
  $mysql_content_db->query_no_result($query);
}

function current_npc_spells_effects($nseid) {
  global $mysql_content_db;
  $effects = array();

  $query = "SELECT spell_effect_id FROM npc_spells_effects_entries WHERE npc_spells_effects_id=$nseid";
  $results = $mysql_content_db->query_mult_assoc($query);

  if ($results) {
    foreach ($results as $result) {
      array_push($effects, $result['spell_effect_id']);
    }
    return $effects;
  }
  else {
    return null;
  }
}

function generateSpellsFile() {
  global $mysql_content_db;
  $spellsfile = "spells_us.txt";
  $count = 0;
  $lastid = 0;
  $success = false;

  $query = "SELECT * FROM spells_new WHERE id <= 44999 ORDER BY id";
  $results = $mysql_content_db->query($query, MYSQLI_USE_RESULT);

  if ($results) {
    $fileOut = fopen($spellsfile, 'w');
    if(!$fileOut) {
      die("Error opening $spellsfile for writing. Make sure the path is writable.");
    }

    while ($spelldata = $results->fetch_assoc()) {
      fwrite($fileOut, implode("^", $spelldata) . "\n");
      $lastid = $spelldata['id'];
      $count++;
    }

    fclose($fileOut);
  }

  $success = ($count > 0) ? true : false;
  $response = array("success" => $success, "count" => $count, "lastid" => $lastid, "spellsfile" => $spellsfile);

  return $response;
}

function generateDBSTRUSFile() {
  global $mysql_content_db;
  $spellsfile = "dbstr_us.txt";
  $count = 0;
  $lastid = 0;
  $success = false;

  $query = "SELECT * FROM db_str ORDER BY `id` ASC, `type` ASC";
  $results = $mysql_content_db->query($query, MYSQLI_USE_RESULT);

  if ($results) {
    $fileOut = fopen($spellsfile, 'w');
    if(!$fileOut) {
      die("Error opening $spellsfile for writing. Make sure the path is writable.");
    }

    while ($spelldata = $results->fetch_assoc()) {
      fwrite($fileOut, implode("^", $spelldata) . "\n");
      $lastid = $spelldata['id'];
      $count++;
    }

    fclose($fileOut);
  }

  $success = ($count > 0) ? true : false;
  $response = array("success" => $success, "count" => $count, "lastid" => $lastid, "spellsfile" => $spellsfile);

  return $response;
}

function importDBSTRUSFile() {
  global $mysql_content_db;
  global $my_root_dbstrus;
  $dbstr_us = "dbstr_us.txt";
  $success = false;

  #Delete table contents for upload...
  $query = "TRUNCATE TABLE db_str";
  $mysql_content_db->query_no_result($query);

  $fileOut = fopen($dbstr_us, 'r');
  if(!$fileOut) {
    die("Error opening $dbstr_us for writing. Make sure the path is writable.");
  }
  
  $query_out = 
    "LOAD DATA INFILE '$my_root_dbstrus/$dbstr_us'
     INTO TABLE db_str
     FIELDS TERMINATED BY '^'
     LINES TERMINATED BY '\n'
    (id,type,value,@dummy)";
	$mysql_content_db->query_no_result($query_out);

    fclose($fileOut);
	
  $success = true;
  $response = array("success" => $success);

  return $response;
}
?>
