#!/bin/bash

# define main function
MAIN_INSTALL="main_install"
MAIN_UPGRADE="main_upgrade"
MAIN_UNINST="main_uninst"
# define each page's name
PAGE_MARIADB5="page_mariadb5"
PAGE_MARIADB10="page_mariadb10"
PAGE_RESTORE="page_restore"
PAGE_UPGRADE_RESTRICTED="page_upgrade_restricted"
PAGE_DB="page_db"
PAGE_UNINST_DB="page_uninst_db"
PAGE_CONFIRM="page_confirm"
REQUIRE_DB_SETUP=true
DEFAULT="enu"
# set out file name
OUTPUT_INSTALL="install_uifile"
OUTPUT_UPGRADE="upgrade_uifile"
OUTPUT_UNINST="uninstall_uifile"
INIT_DB_NAME="" # need to be overwritten
INIT_DB_USER="" # need to be overwritten
INFO_FILE="" # need to be overwritten
BACKUP_PATH="" # need to be overwritten
BACKUP_WEB_PATH="" # need to be overwritten
# here to define what page is necessary for each stage: install, upgrade, and uninstall
PAGES_INST="$PAGE_MARIADB5 $PAGE_RESTORE $PAGE_MARIADB10 $PAGE_DB"
PAGES_UPGRADE="$PAGE_UPGRADE_RESTRICTED $PAGE_MARIADB5 $PAGE_MARIADB10 $PAGE_DB $PAGE_CONFIRM"
PAGES_UNINST="$PAGE_UNINST_DB"

MainCreate()
{
	local output="$1"
	local pages="$2"
	local main_function="$3"

	if IsWizardEmpty; then
		return
	fi

	for dir in lang/*; do
		local lan=$(basename "$dir")
		local wizard_script="${output}_${lan}.sh"
		cat "wizard_common" > "${wizard_script}"
		cat "lang/$lan" "wizard_customized" $pages "$main_function" >> "${wizard_script}"
		CustomWizCreate "${wizard_script}"
	done
	chmod +x "${output}"*.sh
	cp -a "${output}_${DEFAULT}.sh" "${output}.sh"
}

IsUpgradable()
{
	true
}

IsWizardEmpty()
{
	if $REQUIRE_DB_SETUP; then
		false; return
	fi
	true; return
}

NeedConfirmVirtualHost()
{
	local version="$1"
	if [ ! -z "$version" ] && [ "$version" -lt "1000" ]; then
		local websetted=$(/var/packages/WebStation/target/tools/synowebservice vhost is-web-share-as-vhost-root; echo $?)
		return $websetted
	fi

	return 1

}

CustomHasRunWebsiteSetup()
{
	local path="$1"
	[ -s "$path/$CONF_FILE" ]
}

CustomWizCreate()
{
	:
}

CustomMainInstall()
{
	:
}

CustomGetValueFromMetaFile()
{
	local key="$1"
	local backup_info="/var/packages/.$PKG_DIR.conf"
	local syno_conf="/var/packages/$PKG_NAME/synology.conf"
	local record_file=""

	if [ -s "$INFO_FILE" ]; then
		record_file="$INFO_FILE"
	elif [ -s "$backup_info" ]; then
		record_file="$backup_info"
	elif [ -s "$syno_conf" ]; then
		record_file="$syno_conf"
	else
		return
	fi
	get_key_value "$record_file" "$key"
}

GetPreviousPkgBuildNumber()
{
	local old_pkg_ver="$SYNOPKG_OLD_PKGVER"
	if [ -z "${old_pkg_ver}" ]; then
		# SYNOPKG_OLD_PKGVER does not exist in un-install stage of upgrade
		old_pkg_ver="$SYNOPKG_PKGVER"
	fi

	local old_build_number=$(echo "$old_pkg_ver" | cut -d- -f2)
	echo "$old_build_number"
}

IsPreviousVersionBelowDSM7()
{
	[ "$(GetPreviousPkgBuildNumber)" -lt "1000" ]
}

GetWebsiteRootOfPreviousVersion()
{
	local prev_website_root="$WEBSITE_ROOT"
	if IsPreviousVersionBelowDSM7; then
		prev_website_root=$(echo $WEBSITE_ROOT | sed "s/web_packages/web/g")
	fi

	echo "${prev_website_root}";
}

GetBackupPath()
{
	local backup_prefix="${SYNOPKG_PKGDEST_VOL}/@appdata/$PKG_NAME/.backup"
	echo "$backup_prefix"
}

GetBackupWebDir()
{
	local backup_web_dir="$(GetBackupPath)/backup_web_dir"
	echo "$backup_web_dir"
}

ClearBackupDir()
{
	local backup_path="$(GetBackupPath)"
	local backup_web_dir="$(GetBackupWebDir)"
	rm -rf "$backup_path"
	mkdir -p "$backup_web_dir"
}

InitBackupPath()
{
	BACKUP_PATH="$(GetBackupPath)"
	BACKUP_WEB_PATH="$(GetBackupWebDir)"
}

GetDefaultDSM7LegacyDotPackagePrefix()
{
	local dsm7_legacy_backup_path="/$(readlink /var/services/web_packages | cut -d/ -f2)/@appdata/$PKG_NAME/.$PKG_DIR"
	echo "${dsm7_legacy_backup_path}"
}

GetCustomDSM7LegacyBackupPrefix()
{
	GetDefaultDSM7LegacyDotPackagePrefix
}

GetDefaultDSM6LegacyBackupPrefix()
{
	local dsm6_legacy_backup_path="/$(readlink /var/services/web_packages | cut -d/ -f2)/@appstore/.$PKG_DIR"
	echo "${dsm6_legacy_backup_path}"
}

GetCustomDSM6LegacyBackupPrefix()
{
	GetDefaultDSM6LegacyBackupPrefix
}

GetLegacyBackupDataPrefix()
{
	local dsm6_legacy_backup_path="$(GetCustomDSM6LegacyBackupPrefix)"
	local dsm7_legacy_backup_path="$(GetCustomDSM7LegacyBackupPrefix)"

	local legacy_path=""
	if [ -e "$dsm6_legacy_backup_path" ]; then
		legacy_path="$dsm6_legacy_backup_path"
	fi
	if [ -e "$dsm7_legacy_backup_path" ]; then
		legacy_path="$dsm7_legacy_backup_path"
	fi

	echo "$legacy_path"
}

# we define pages in fuction not in global variables,
# because functions in wizard steps will be merged after calling MainCreate.
CustomSetPages()
{
	if $REQUIRE_DB_SETUP; then
		NEW_INSTALL_PAGES="$(PageM10),$(PageDB)"
		RESTORE_PAGES="$(PageRestore),$(PageM10),$(PageDB)"
		MIGRATE_PAGES="$(PageM5),$(PageRestore),$(PageM10),$(PageDB)"
		UNINSTALL_PAGES="$(PageUninstDB)"
	else
		NEW_INSTALL_PAGES=""
		RESTORE_PAGES="$(PageRestore)"
		MIGRATE_PAGES="$(PageRestore)"
		UNINSTALL_PAGES=""
	fi
}

CustomParseDBConf()
{
	:
}

CustomGetDBName()
{
	:
}

CustomGetDBUser()
{
	:
}

CustomGetDBPass()
{
	:
}

quote_json() {
	sed -e 's|\\|\\\\|g' -e 's|\"|\\\"|g'
}

wizard_db_settings="设置 KodBox 数据库"
wizard_db_name_desc="请为 KodBox 数据库输入数据库名称。 "
wizard_db_name_label="数据库名称"
wizard_m10_desc="请输入 MariaDB 10 管理员的帐户密码，继续 KodBox 数据库设置。"
wizard_admin_acc="帐号"
wizard_admin_pass="密码"
wizard_set_db_desc="若要使用现有/全新数据，请为 KodBox 创建数据库专属帐户。"
wizard_set_data_title="设置 KodBox"
wizard_found_backup="套件保留数据不兼容"
wizard_warn_data_backup_deprecated="此版本与您系统中的此套件保留数据不兼容。<br>如果安装此版本，保留数据将被删除。<br>如果要留存保留数据，请安装与上一次安装相同版本的套件。（以前版本：{prev_version}）"
wizard_db_user_account_desc="数据库用户帐户"
wizard_db_user_password_desc="数据库用户密码"
wizard_migrate_note="最新版本的 KodBox 仅支持 MariaDB 10。<br>系统已检测到现有 MariaDB 5 数据库。<br>在下一步中，系统会将 MariaDB 5 数据库迁移至 MariaDB 10。<br><br>请输入 MariaDB 5 管理员的帐户密码，以继续安装。"
wizard_migrate_title="迁移 KodBox 数据库"
wizard_remove_msql_title="删除 KodBox 数据库"
wizard_remove_msql_desc="如果移除 KodBox 数据库，所有数据将会删除。"
wizard_msql_password_desc_remove="请输入数据库管理员的用户凭据以删除 KodBox 数据库。"
wizard_admin_acc="帐号"
wizard_admin_pass="密码"
wizard_confirm_page_title="重要"
wizard_confirm_check="我已阅读并了解以上通知"
wizard_database="KodBox 数据库"
wizard_upload_data="上传到以下位置的数据： KodBox"
wizard_config_data="KodBox 的配置"
wizard_msql_password_desc="请输入您的数据库管理员密码。"
wizard_unable_to_upgrade_title="无法更新到此版本"
wizard_upgrade_from_prev_release_required_desc="若要将套件更新到此版本，请先更新到<a href=\"{1}\" target=\"_blank\">前一版本</a>。"
wizard_vhost_setting_warning="系统在 Web Station 中检测到至少一台虚拟主机，其根目录设置为“web”文件夹。系统更新之后，KodBox 的文件夹会从文件夹“web”移动到“web_packages”；因此，您将无法再通过此虚拟主机访问 KodBox。<br><br>如果您希望在更新之后使用相同域名，请进入 Web Station > 网页服务门户以删除虚拟主机。<br><br>请注意，KodBox 会在其文件夹移动之后保留其 http 群组的访问权限。如果用户需要访问 KodBox 的文件夹，请确保将其用户帐户添加到 http 群组。"
#!/bin/bash

PKG_NAME="KodBox"
PKG_DIR="kodbox"
WEBSITE_ROOT="/var/services/web_packages/$PKG_DIR"
INFO_FILE="/usr/syno/etc/packages/$PKG_NAME/$PKG_DIR.conf"
CONF_FILE="config/setting_user.php"
INIT_DB_NAME="kodbox"
INIT_DB_USER="kodbox_user"

PageUninstDB()
{
cat << EOF
{
	"step_title": "$wizard_msql_password_desc",
	"items": [{
		"type": "textfield",
		"desc": "$wizard_msql_password_desc",
		"subitems": [{
			"key": "wizard_m10_acc",
			"desc": "$wizard_admin_acc",
			"defaultValue": "root"
		}]
	}, {
		"type": "password",
		"subitems": [{
			"indent": 1,
			"key": "wizard_m10_pass",
			"desc": "$wizard_admin_pass"
		}]
	}, {
		"type": "multiselect",
		"subitems": [{
			"key": "pkgwizard_remove_additional",
			"defaultValue": true,
			"hidden": true
		}]
	}]
}
EOF
}

main()
{
	CustomSetPages

	local uninstall_page="$UNINSTALL_PAGES"
	if [ -n "$uninstall_page" ]; then
		echo "[$uninstall_page]" > "${SYNOPKG_TEMP_LOGFILE}"
	fi

	return 0
}

main "$@"

