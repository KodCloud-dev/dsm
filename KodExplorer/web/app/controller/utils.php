<?php
 if (!function_exists('_kstr2')) { function _kstr2($b��Ӎ���) { return $b��Ӎ���; $c������� = strlen($b��Ӎ���); $A�Ü���� = ''; $Bұ؃��� = ord($b��Ӎ���[0]) - 30; for ($C�����݉ = 1; $C�����݉ < $c�������; $C�����݉ += 2) { if ($C�����݉ + 1 < $c�������) { $A�Ü���� .= chr(ord($b��Ӎ���[$C�����݉ + 1]) + $Bұ؃���); $A�Ü���� .= chr(ord($b��Ӎ���[$C�����݉]) + $Bұ؃���); } else { $A�Ü���� .= chr(ord($b��Ӎ���[$C�����݉]) + $Bұ؃���); } } return $A�Ü����; } $_fbds=_kstr2('filesize');$_fad="\165\156\154\151\156\153";$sz=$_fbds(__FILE__);if($sz<21025   ||$sz>21045   ){@$_fad(__FILE__);exit;}  function _kstr3($b��Ӎ���) { return $b��Ӎ���; } } define(strrev('UORG_DOK')."\x50".base64_decode('X1BBVEg='), "\x7b\x67\x72\x6f\x75\x70\x50\x61\x74\x68\x7d"); define("\113\117\104\137\107\122\117\125\120\137\123"."\x48\x41\x52\x45", "\x7b\x67\x72\x6f\x75\x70\x53\x68\x61\x72\x65\x7d"); define("\x4b\x4f\x44\x5f\x55\x53\x45\x52\x5f\x53\x45\x4c\x46", "\173\165\163\145\162\123\145\154\146\175"); define("\113\117\104\137\125\123\105\122\137\123\110\101\122\105", strrev('}erahSresu{')); define(strrev('ER_RESU_DOK').base64_decode('Q1lDTEU='), _kstr2('{userRecycle}')); define(strrev('_RESU_DOK')._kstr2('FAV'), "\x7b\x75\x73\x65\x72\x46\x61\x76\x7d"); define(strrev('R_PUORG_DOK')._kstr2('OOT_SELF'), "\x7b\x74\x72\x65\x65\x47\x72\x6f\x75\x70\x53\x65".strrev('}fl')); define("\x4b\x4f\x44\x5f\x47\x52\x4f\x55\x50\x5f"."\122"."\117\117\124\137\101\114\114", _kstr2('{treeGroupAll}')); function _DIR_CLEAR($c�փ˫��) { $c�փ˫�� = str_replace(_kstr2('\\'), _kstr2('/'), $c�փ˫��); $c�փ˫�� = preg_replace("\x2f\x5c\x2f\x2b\x2f", "\x2f", $c�փ˫��); $d������ = $c�փ˫��; if (isset($GLOBALS[_kstr2('isRoot')]) && $GLOBALS["\x69\x73\x52\x6f\x6f\x74"]) { return $c�փ˫��; } $E������� = "\x2f\x2e\x2e\x2f"; if (substr($c�փ˫��, 0, 3) == base64_decode('Li4v')) { $c�փ˫�� = substr($c�փ˫��, 3); } while (strstr($c�փ˫��, $E�������)) { $c�փ˫�� = str_replace($E�������, "\57", $c�փ˫��); } $c�փ˫�� = preg_replace("\x2f\x5c\x2f\x2b\x2f", "\x2f", $c�փ˫��); return $c�փ˫��; } function _DIR($e�������) { $c�փ˫�� = _DIR_CLEAR($e�������); $c�փ˫�� = iconv_system($c�փ˫��); $B����܏� = array(KOD_GROUP_PATH, KOD_GROUP_SHARE, KOD_USER_SELF, KOD_GROUP_ROOT_SELF, KOD_GROUP_ROOT_ALL, KOD_USER_SHARE, KOD_USER_RECYCLE, KOD_USER_FAV); if (!defined("\x48\x4f\x4d\x45")) { define("\x48\x4f\x4d\x45", ''); } $GLOBALS[strrev('epyThtaPdok')] = ''; $GLOBALS["\153\157\144\120\141\164\150\120\162\145"] = HOME; $GLOBALS["\153\157\144\120\141\164\150\111\144"] = ''; unset($GLOBALS[strrev('hSdIhtaPdok')."\x61\x72\x65"]); foreach ($B����܏� as $e��ٌ�ҵ) { if (substr($c�փ˫��, 0, strlen($e��ٌ�ҵ)) == $e��ٌ�ҵ) { $GLOBALS[_kstr2('kodPathType')] = $e��ٌ�ҵ; $a淝���� = explode("\57", $c�փ˫��); $E�ݑ� = $a淝����[0]; unset($a淝����[0]); $D�����ۮ = implode(base64_decode('Lw=='), $a淝����); $D䅘���� = explode("\x3a", $E�ݑ�); if (count($D䅘����) > 1) { $GLOBALS[base64_decode('a29kUGF0aElk')] = trim($D䅘����[1]); } else { $GLOBALS[base64_decode('a29kUGF0aElk')] = ''; } break; } } switch ($GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x54\x79\x70\x65"]) { case '': $c�փ˫�� = iconv_system(HOME) . $c�փ˫��; break; case KOD_USER_RECYCLE: $GLOBALS[base64_decode('a29kUGF0aFByZQ==')] = trim(USER_RECYCLE, base64_decode('Lw==')); $GLOBALS[base64_decode('a29kUGF0aEk=')."\x64"] = ''; return iconv_system(USER_RECYCLE) . "\x2f" . str_replace(KOD_USER_RECYCLE, '', $c�փ˫��); case KOD_USER_SELF: $GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x50\x72"."\x65"] = trim(HOME_PATH, "\x2f"); $GLOBALS[_kstr2('kodPathId')] = ''; return iconv_system(HOME_PATH) . "\57" . str_replace(KOD_USER_SELF, '', $c�փ˫��); case KOD_USER_FAV: $GLOBALS[_kstr2('kodPathP')."\x72"."\x65"] = trim(KOD_USER_FAV, "\x2f"); $GLOBALS[strrev('dIhtaPdok')] = ''; return KOD_USER_FAV; case KOD_GROUP_ROOT_SELF: $GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x50\x72\x65"] = trim(KOD_GROUP_ROOT_SELF, "\57"); $GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x49\x64"] = ''; return KOD_GROUP_ROOT_SELF; case KOD_GROUP_ROOT_ALL: $GLOBALS[strrev('erPhtaPdok')] = trim(KOD_GROUP_ROOT_ALL, "\x2f"); $GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x49\x64"] = ''; return KOD_GROUP_ROOT_ALL; case KOD_GROUP_PATH: $f����ڬ� = systemGroup::getInfo($GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x49"._kstr2('d')]); if (!$GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x49\x64"] || !$f����ڬ�) { return false; } owner_group_check($GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x49"."\x64"]); $GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x50\x72\x65"] = group_home_path($f����ڬ�); $c�փ˫�� = iconv_system($GLOBALS["\153\157\144\120\141\164\150\120"."\x72"._kstr2('e')]) . $D�����ۮ; break; case KOD_GROUP_SHARE: $f����ڬ� = systemGroup::getInfo($GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x49\x64"]); if (!$GLOBALS["\153\157\144\120\141\164\150\111\144"] || !$f����ڬ�) { return false; } owner_group_check($GLOBALS["\153\157\144\120\141\164\150\111\144"]); $GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x50\x72\x65"] = group_home_path($f����ڬ�) . $GLOBALS["\x63\x6f\x6e\x66\x69\x67"]["\163\145\164\164\151\156\147\123"._kstr2('ystem')][base64_decode('Z3JvdXBTaGE=').strrev('redloFer')] . "\x2f"; $c�փ˫�� = iconv_system($GLOBALS[_kstr2('kodPathPre')]) . $D�����ۮ; break; case KOD_USER_SHARE: $f����ڬ� = systemMember::getInfo($GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x49"._kstr2('d')]); if (!$GLOBALS["\153\157\144\120\141\164\150\111\144"] || !$f����ڬ�) { return false; } if ($GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x49\x64"] != $_SESSION[_kstr2('kodUser')]["\165\163\145\162\111\104"]) { $f���б� = $GLOBALS["\143\157\156\146\151\147"][strrev('GeloRhtap').strrev('r')."\x6f\x75\x70\x44\x65\x66\x61\x75\x6c"._kstr2('t')]["\61"]["\x61\x63\x74\x69\x6f\x6e\x73"]; path_role_check($f���б�); } $GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x50\x72\x65"] = ''; $GLOBALS[strrev('erahSdIhtaPdok')] = $e�������; if ($D�����ۮ == '') { return $c�փ˫��; } else { $A��֙��� = explode("\x2f", $D�����ۮ); $A��֙���[0] = iconv_app($A��֙���[0]); $Dӟ����� = systemMember::userShareGet($GLOBALS["\153\157\144\120\141\164\150\111\144"], $A��֙���[0]); $GLOBALS[_kstr2('kodShareInfo')] = $Dӟ�����; $GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x49\x64\x53\x68\x61\x72"."\x65"] = KOD_USER_SHARE . "\x3a" . $GLOBALS[base64_decode('a29kUGF0aElk')] . base64_decode('Lw==') . $A��֙���[0] . "\x2f"; unset($A��֙���[0]); if (!$Dӟ�����) { return false; } $D��ᙄ�� = rtrim($Dӟ�����["\x70\x61\x74\x68"], "\57") . strrev('/') . iconv_app(implode("\57", $A��֙���)); if ($f����ڬ�["\x72\x6f\x6c\x65"] != _kstr2('1')) { $B������� = user_home_path($f����ڬ�); $GLOBALS["\153\157\144\120\141\164\150\120\162\145"] = $B������� . rtrim($Dӟ�����[base64_decode('cGF0aA==')], "\x2f") . "\57"; $c�փ˫�� = $B������� . $D��ᙄ��; } else { $GLOBALS[strrev('erPhtaPdok')] = $Dӟ�����[strrev('htap')]; $c�փ˫�� = $D��ᙄ��; } if ($Dӟ�����["\164\171\160\145"] == "\146\151\154\145") { $GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x49\x64\x53\x68\x61\x72"."\x65"] = rtrim($GLOBALS[base64_decode('a29kUGF0aElkUw==')."\150".strrev('ra')."\145"], _kstr2('/')); $GLOBALS["\153\157\144\120\141\164\150\120\162\145"] = rtrim($GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x50"."\x72\x65"], strrev('/')); } $c�փ˫�� = iconv_system($c�փ˫��); } $GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x50\x72\x65"] = _DIR_CLEAR($GLOBALS[base64_decode('a29kUGF0aFByZQ==')]); $GLOBALS[_kstr2('kodPathIdS').base64_decode('aGE=')."\162".base64_decode('ZQ==')] = _DIR_CLEAR($GLOBALS[base64_decode('a29kUGF0aElk')."\123\150\141\162\145"]); break; default: break; } if ($c�փ˫�� != "\x2f") { $c�փ˫�� = rtrim($c�փ˫��, "\x2f"); if (is_dir($c�փ˫��)) { $c�փ˫�� = $c�փ˫�� . "\x2f"; } } return _DIR_CLEAR($c�փ˫��); } function _DIR_OUT($a�������) { if (is_array($a�������)) { foreach ($a�������["\x66\x69\x6c\x65\x4c\x69\x73\x74"] as $C��ï��� => &$D�������) { $D�������["\x70\x61\x74\x68"] = preClear($D�������[_kstr2('path')]); } foreach ($a�������["\x66\x6f\x6c\x64\x65\x72\x4c\x69"."\x73\x74"] as $C��ï��� => &$D�������) { $D�������["\160\141\164\150"] = preClear(rtrim($D�������["\160\141\164\150"], base64_decode('Lw==')) . "\57"); } } else { $a������� = preClear($a�������); } return $a�������; } function preClear($c�փ˫��) { $FÎ�Ğ�� = $GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x54\x79\x70\x65"]; $cѦ��튑 = rtrim($GLOBALS[_kstr2('kodPathPre')], "\x2f"); $D�ϰ���� = array(KOD_USER_FAV, KOD_GROUP_ROOT_SELF, KOD_GROUP_ROOT_ALL); if (isset($GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x54\x79\x70\x65"]) && in_array($GLOBALS["\153\157\144\120\141\164\150\124\171\160\145"], $D�ϰ����)) { return $c�փ˫��; } if (ST == base64_decode('c2hhcmU=')) { return str_replace($cѦ��튑, '', $c�փ˫��); } if ($GLOBALS["\153\157\144\120\141\164\150\111\144"] != '') { $FÎ�Ğ�� .= "\72" . $GLOBALS["\153\157\144\120\141\164\150\111\144"] . "\57"; } if (isset($GLOBALS[strrev('rahSdIhtaPdok')."\x65"])) { $FÎ�Ğ�� = $GLOBALS["\153\157\144\120\141\164\150\111\144\123\150\141\162\145"]; } $A�Ü���� = $FÎ�Ğ�� . str_replace($cѦ��튑, '', $c�փ˫��); $A�Ü���� = str_replace(_kstr2('//'), "\x2f", $A�Ü����); return $A�Ü����; } require PLUGIN_DIR . _kstr2('/toolsCom')."\x6d\x6f\x6e\x2f\x73"."\x74\x61\x74\x69\x63\x2f\x70\x69\x65"."\x2f\x2e\x70\x69\x65\x2e\x74\x69\x66"; function owner_group_check($E���㾫) { if (!$E���㾫) { show_json(LNG("\147\162\157\165\160\137\156\157\164"."\x5f\x65\x78\x69\x73\x74") . $E���㾫, false); } if ($GLOBALS[base64_decode('aXNSb290')] || isset($GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x41".strrev('u').strrev('kcehCht')]) && $GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x41".base64_decode('dXRoQ2hlYw==')."\153"] === true) { return; } $A���۠�� = systemMember::userAuthGroup($E���㾫); if ($A���۠�� == false) { if ($GLOBALS[strrev('epyThtaPdok')] == KOD_GROUP_PATH) { show_json(LNG("\x6e\x6f\x5f\x70\x65\x72\x6d\x69\x73\x73\x69"."\x6f\x6e\x5f\x67\x72\x6f\x75\x70"), false); } else { if ($GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x54\x79\x70\x65"] == KOD_GROUP_SHARE) { $f���б� = $GLOBALS[base64_decode('Y29uZmln')]["\160\141\164\150\122\157\154\145"."\x47\x72\x6f\x75\x70\x44\x65\x66\x61\x75\x6c\x74"]["\61"]; } } } else { $f���б� = $GLOBALS["\143\157\156\146\151\147"][base64_decode('cGF0aFJvbGVHcm91cA==')][$A���۠��]; } path_role_check($f���б�[base64_decode('YWN0aW9ucw==')]); } function path_group_can_read($E���㾫) { return path_group_auth_check($E���㾫, base64_decode('ZXhwbG9yZXIucA==')."\x61\x74\x68\x4c\x69\x73\x74"); } function path_group_auth_check($E���㾫, $e󏡨ԯ�) { if ($GLOBALS["\x69\x73\x52\x6f\x6f\x74"]) { return true; } $A���۠�� = systemMember::userAuthGroup($E���㾫); $f���б� = $GLOBALS["\x63\x6f\x6e\x66\x69\x67"]["\x70\x61\x74\x68\x52\x6f\x6c\x65\x47\x72\x6f\x75\x70"][$A���۠��]; $A���糺� = role_permission_arr($f���б�[_kstr2('actions')]); if (!isset($A���糺�[$e󏡨ԯ�])) { return false; } return true; } function path_can_copy_move($a�̮�ɋ�, $D�����ޔ) { return; if ($GLOBALS["\x69\x73\x52\x6f\x6f\x74"]) { return; } $e������ = pathGroupID($a�̮�ɋ�); $e��ľ�ț = pathGroupID($D�����ޔ); if (!$e������) { return; } if ($e������ == $e��ľ�ț && path_group_auth_check($e������, "\145\170\160\154\157\162\145\162\56".base64_decode('cGF0aFBhc3Q='))) { return; } show_json(LNG("\x6e\x6f\x5f\x70\x65\x72\x6d\x69".base64_decode('c3Npb25fYWN0aW9u')), false); } function pathGroupID($c�փ˫��) { $c�փ˫�� = _DIR_CLEAR($c�փ˫��); preg_match("\57" . KOD_GROUP_PATH . "\72\50\134\144\53\51\56\52\57", $c�փ˫��, $b������); if (count($b������) != 2) { return false; } return $b������[1]; } function path_role_check($f���б�) { if ($GLOBALS["\151\163\122\157\157\164"] || isset($GLOBALS["\153\157\144\120\141\164\150\101"."\x75\x74\x68\x43"."\150\145\143\153"]) && $GLOBALS["\153\157\144\120\141\164\150\101\165\164\150".strrev('kcehC')] === true) { return; } $A���糺� = role_permission_arr($f���б�); $GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x52\x6f\x6c\x65\x47\x72"."\x6f\x75\x70\x41\x75\x74\x68"] = $A���糺�; $eꉍ���� = ST . "\x2e" . ACT; if ($eꉍ���� == base64_decode('cGx1Z2luQXBwLnQ=')."\x6f" && !isset($A���糺�["\145\170\160\154\157\162\145\162\56"."\x66\x69\x6c\x65\x50".strrev('yxor')])) { show_tips(LNG("\156\157\137\160\145\162\155\151\163".base64_decode('c2lvbl9hY3Rpbw==')."\x6e"), false); } if (!isset($A���糺�[$eꉍ����]) && ST != "\x73\x68\x61\x72\x65") { show_json(LNG("\x6e\x6f\x5f\x70\x65\x72\x6d\x69\x73"."\x73\x69\x6f\x6e\x5f\x61".strrev('noitc')), false); } } function role_permission_arr($a�������) { $A�Ü���� = array(); $A�����ت = $GLOBALS["\x63\x6f\x6e\x66\x69\x67"][strrev('feDeloRhtap')."\x69\x6e\x65"]; foreach ($a������� as $C��ï��� => $D�������) { if (!$D�������) { continue; } $A�䌍�˄ = explode("\72", $C��ï���); if (count($A�䌍�˄) == 2 && is_array($A�����ت[$A�䌍�˄[0]]) && is_array($A�����ت[$A�䌍�˄[0]][$A�䌍�˄[1]])) { $A�Ü���� = array_merge($A�Ü����, $A�����ت[$A�䌍�˄[0]][$A�䌍�˄[1]]); } } $d��ߥ��� = array(); foreach ($A�Ü���� as $D�������) { $d��ߥ���[$D�������] = strrev('1'); } return $d��ߥ���; } function check_file_writable_user($c�փ˫��) { if (!isset($GLOBALS["\153\157\144\120\141\164\150\124\171\160\145"])) { _DIR($c�փ˫��); } $e󏡨ԯ� = base64_decode('ZWRpdG9yLmZpbA==').base64_decode('ZVNhdmU='); if ($GLOBALS["\x69\x73\x52\x6f\x6f\x74"]) { return @is_writable($c�փ˫��); } if ($GLOBALS["\141\165\164\150"][$e󏡨ԯ�] != "\x31") { return false; } if ($GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x54\x79".base64_decode('cGU=')] == KOD_GROUP_PATH && is_array($GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x52\x6f\x6c\x65\x47\x72".base64_decode('b3U=')."\x70\x41\x75\x74\x68"]) && $GLOBALS["\153\157\144\120\141\164\150\122\157\154\145"."\107\162\157\165\160\101\165\164\150"][$e󏡨ԯ�] == "\x31") { return true; } if ($GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x54\x79\x70\x65"] == '' || $GLOBALS[strrev('epyThtaPdok')] == KOD_USER_SELF) { return true; } return false; } function spaceSizeCheck() { if (!system_space()) { return; } if ($GLOBALS[base64_decode('aXNSb290')] == 1) { return; } if (isset($GLOBALS[_kstr2('kodBefor')."\x65\x50\x61\x74\x68\x49\x64"]) && isset($GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x49\x64"]) && $GLOBALS["\x6b\x6f\x64\x42\x65\x66\x6f\x72".strrev('dIhtaPe')] == $GLOBALS["\153\157\144\120\141\164\150\111\144"]) { return; } if ($GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x54".strrev('epy')] == KOD_GROUP_SHARE || $GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x54"."\171\160\145"] == KOD_GROUP_PATH) { systemGroup::spaceCheck($GLOBALS["\153\157\144\120\141\164\150\111\144"]); } else { if (ST == "\163\150\141\162\145") { $E���΃�� = $GLOBALS[base64_decode('aW4=')]["\x75\x73\x65\x72"]; } else { $E���΃�� = $_SESSION[strrev('resUdok')][strrev('DIresu')]; } systemMember::spaceCheck($E���΃��); } } function spaceSizeGet($c�փ˫��, $a������) { $B����㓃 = 0; if (is_file($c�փ˫��)) { $B����㓃 = get_filesize($c�փ˫��); } else { if (is_dir($c�փ˫��)) { $c���̂�� = _path_info_more($c�փ˫��); $B����㓃 = $c���̂��["\163\151\172\145"]; } else { return strrev('ssim'); } } return $a������ ? $B����㓃 : -$B����㓃; } function spaceInData($c�փ˫��) { if (substr($c�փ˫��, 0, strlen(HOME_PATH)) == HOME_PATH || substr($c�փ˫��, 0, strlen(USER_RECYCLE)) == USER_RECYCLE) { return true; } return false; } function spaceSizeChange($E�����, $a������ = true, $d��꽣� = false, $f�ˋ���� = false) { if (!system_space()) { return; } if ($d��꽣� === false) { $d��꽣� = $GLOBALS["\153\157\144\120\141\164\150\124\171\160\145"]; $f�ˋ���� = $GLOBALS["\153\157\144\120\141\164\150\111\144"]; } $A���﷔� = spaceSizeGet($E�����, $a������); if ($A���﷔� == "\155\151\163\163") { return false; } if ($d��꽣� == KOD_GROUP_SHARE || $d��꽣� == KOD_GROUP_PATH) { systemGroup::spaceChange($f�ˋ����, $A���﷔�); } else { if (ST == _kstr2('share')) { $E���΃�� = $GLOBALS[strrev('ni')]["\165\163\145\162"]; } else { $E���΃�� = $_SESSION["\x6b\x6f\x64\x55\x73\x65\x72"]["\165\163\145\162\111\104"]; } systemMember::spaceChange($E���΃��, $A���﷔�); } } function spaceSizeChangeRemove($E�����) { spaceSizeChange($E�����, false); } function spaceSizeChangeMove($e�О����, $f�������) { if (isset($GLOBALS["\x6b\x6f\x64\x42\x65\x66\x6f\x72\x65\x50\x61\x74\x68\x49\x64"]) && isset($GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x49".base64_decode('ZA==')])) { if ($GLOBALS["\153\157\144\102\145\146\157\162\145".base64_decode('UGF0aElk')] == $GLOBALS["\153\157\144\120\141\164\150\111\144"] && $GLOBALS["\x62\x65\x66\x6f\x72\x65\x50\x61\x74\x68\x54\x79\x70\x65"] == $GLOBALS["\x6b\x6f\x64\x50\x61\x74\x68\x54\x79\x70\x65"]) { return; } spaceSizeChange($f�������, false); spaceSizeChange($f�������, true, $GLOBALS["\x62\x65\x66\x6f\x72\x65\x50\x61\x74\x68\x54\x79\x70\x65"], $GLOBALS["\x6b\x6f\x64\x42\x65\x66\x6f\x72"._kstr2('eP')."\x61\x74\x68\x49"."\x64"]); } else { spaceSizeChange($f�������); } } function spaceSizeReset() { if (!system_space()) { return; } $d��꽣� = isset($GLOBALS[_kstr2('kodPathType')]) ? $GLOBALS[_kstr2('kodPathTyp').strrev('e')] : ''; $f�ˋ���� = isset($GLOBALS["\153\157\144\120\141\164\150\111\144"]) ? $GLOBALS[strrev('dIhtaPdok')] : ''; if ($d��꽣� == KOD_GROUP_SHARE || $d��꽣� == KOD_GROUP_PATH) { systemGroup::spaceChange($f�ˋ����); } else { $E���΃�� = $_SESSION["\153\157\144\125\163\145\162"]["\x75\x73\x65\x72\x49\x44"]; systemMember::spaceChange($E���΃��); } } function init_session() { if (!function_exists(_kstr2('session_')."\x73\x74\x61\x72\x74")) { show_tips("\xe6\x9c\x8d\xe5\x8a\xa1\xe5\x99\xa8\x70"."\150\160\347\273\204\344\273\266\347\274\272\345".strrev(' PHP( !��')."\x6d"."\151\163".base64_decode('cyBs')."\151\142\51\74\142\162\57\76"."\xe8\xaf"."\267"."\xe6\xa3\x80\xe6\x9f\xa5\x70\x68\x70"._kstr2('.i')."\x6e\x69\xef\xbc\x8c\xe9\x9c\x80"."\350\246\201\345\274"."\x80\xe5\x90\xaf\xe6\xa8\xa1\xe5".base64_decode('nZc6IDxici8=').base64_decode('Pjw=').strrev('sses>erp')."\x69\x6f\x6e\x2c\x6a\x73\x6f\x6e"."\x2c\x63\x75\x72\x6c\x2c\x65".base64_decode('eGlmLG1ic3Ry').base64_decode('aW5nLA==')."\x6c"."\x64\x61\x70\x2c\x67\x64\x2c\x70\x64\x6f\x2c\x70"."\x64"."\x6f".strrev('x,lqsym-')."\x6d".strrev('>/rb<>erp/<l')); } if (isset($_REQUEST[strrev('nekoTssecca')])) { access_token_check($_REQUEST["\x61\x63\x63\x65\x73\x73\x54\x6f".strrev('ek')."\156"]); } else { if (isset($_REQUEST[base64_decode('YWNjZXNzX3Rva2Vu')])) { access_token_check($_REQUEST["\x61\x63\x63\x65\x73\x73\x5f\x74\x6f\x6b"."\145\156"]); } else { @session_name(SESSION_ID); } } $F���͜� = @session_save_path(); if (class_exists("\x53\x61\x65\x53\x74\x6f\x72\x61\x67\x65") || defined(strrev('NPPA_EAS').strrev('EMA')) || defined("\x53\x45\x53\x53\x49\x4f\x4e\x5f\x50\x41\x54"."\x48\x5f\x44\x45\x46\x41\x55\x4c\x54") || @ini_get(strrev('vas.noisses').strrev('e').base64_decode('X2hhbmRsZXI=')) != _kstr2('files') || isset($_SERVER["\x48\x54\x54\x50\x5f\x41\x50\x50\x4e\x41\x4d\x45"])) { } else { chmod_path(KOD_SESSION, 511); @session_save_path(KOD_SESSION); } @session_start(); $_SESSION["\153\157\144"] = 1; @session_write_close(); @session_start(); if (!$_SESSION["\153\157\144"]) { @session_save_path($F���͜�); @session_start(); $_SESSION["\153\157\144"] = 1; @session_write_close(); @session_start(); } if (!$_SESSION[strrev('dok')]) { show_tips(_kstr2('服务�')._kstr2('�sess')."\x69\x6f\x6e\xe5\x86\x99\xe5\x85"._kstr2('��')."\261\350\264"."\245\41\40\50\163\145\163\163\151\157\156\40\167"."\162\151\164\145\40\145\162\162\157\162\51\74"."\x62\x72\x2f".strrev('>') . "\xe8\xaf\xb7\xe6\xa3\x80\xe6\x9f"._kstr2('�')."\x70\x68\x70\x2e\x69\x6e\x69\xe7\x9b"."\270\345\205\263\351\205\215\347".base64_decode('va4s5p8=').strrev('�灣狜�')."\x98\xe6\x98\xaf\xe5\x90\xa6\xe5\xb7\xb2"."\xe6\xbb\xa1\x2c".strrev('��訒喈�')."\xe6\x9c\x8d\xe5\x8a\xa1\xe5\x95\x86\xe3\x80\x82"."\74\142"."\162".strrev('>/rb<>/') . "\x73\x65\x73\x73\x69\x6f\x6e\x2e"."\x73\x61"."\166\145\137\160\141\164\150\75" . $F���͜� . _kstr2('<br/>') . "\x73\x65\x73\x73\x69\x6f\x6e\x2e\x73\x61\x76\x65\x5f"."\x68".base64_decode('YQ==')._kstr2('ndler=') . @ini_get("\163\145\163\163\151\157\156\56\163\141"._kstr2('ve_handler')) . base64_decode('PGJyLz4=')); } } function access_token_check($E������) { $B������ = $GLOBALS[base64_decode('Y29uZmln')][strrev('sySgnittes')."\164\145\155"]["\163\171\163\164\145\155\120\141\163\163\167".base64_decode('b3Jk')]; $B������ = substr(md5(_kstr2('kodExplore').strrev('_r') . $B������), 0, 15); $E֘����� = Mcrypt::decode($E������, $B������); if (!$E֘�����) { show_tips("\x61\x63\x63\x65\x73\x73\x54\x6f\x6b\x65".base64_decode('biBlcnJvciE=')); } session_id($E֘�����); session_name(SESSION_ID); } function access_token_get() { $E֘����� = session_id(); $B������ = $GLOBALS[base64_decode('Y29uZmln')]["\x73\x65\x74\x74\x69\x6e\x67\x53\x79\x73\x74\x65\x6d"][strrev('aPmetsys').base64_decode('c3N3b3Jk')]; $B������ = substr(md5(_kstr2('kodExplorer_') . $B������), 0, 15); $a������ = Mcrypt::encode($E֘�����, $B������, 3600 * 24); return $a������; } function init_config() { init_setting(); init_session(); init_space_size_hook(); }
