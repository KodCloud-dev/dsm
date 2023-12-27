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

wizard_db_settings="Set up KodBox Database"
wizard_db_name_desc="Please enter the database name for KodBox."
wizard_db_name_label="Database name"
wizard_m10_desc="Please enter the administrator credentials of MariaDB 10 to proceed with KodBox database settings."
wizard_admin_acc="Account"
wizard_admin_pass="Password"
wizard_set_db_desc="Please create a new exclusive database account for KodBox to use existing/new data."
wizard_set_data_title="Set up KodBox"
wizard_found_backup="Package reserved data incompatible"
wizard_warn_data_backup_deprecated="The reserved data of this package in your system is not compatible with this version.<br>If you install this version, the reserved data will be deleted.<br>If you wish to keep the reserved data, install the package of the same version as your previous installation. (Previous version: {prev_version})"
wizard_db_user_account_desc="Database user account"
wizard_db_user_password_desc="Database user password"
wizard_migrate_note="The latest version of KodBox only supports MariaDB 10.<br>The system has detected an existing MariaDB 5 database.<br>In the next step, your database will be migrated from MariaDB 5 to MariaDB 10.<br><br>Please enter the administrator credentials of MariaDB 5 to proceed with the installation."
wizard_migrate_title="Migrate KodBox Database"
wizard_remove_msql_title="Remove KodBox database"
wizard_remove_msql_desc="If the KodBox database is removed, all the data will be deleted."
wizard_msql_password_desc_remove="Please enter the credentials of the database administrator to remove the KodBox database."
wizard_admin_acc="Account"
wizard_admin_pass="Password"
wizard_confirm_page_title="Important"
wizard_confirm_check="I read and understood the notice above"
wizard_database="KodBox database"
wizard_upload_data="Data uploaded to KodBox"
wizard_config_data="Configurations in KodBox"
wizard_msql_password_desc="Please enter your database administrator password."
wizard_unable_to_upgrade_title="Unable to update to this version"
wizard_upgrade_from_prev_release_required_desc="Before updating the package to this version, please update it to its <a href=\"{1}\" target=\"_blank\">preceding version</a>."
wizard_vhost_setting_warning="System detected at least one virtual host in Web Station whose root directory is set to the \\\"web\\\" folder. After the system update, KodBox's folder will be moved from the folder \\\"web\\\" to \\\"web_packages\\\"; as a result, you will no longer be able to access KodBox via this virtual host.<br><br>If you wish to use the same domain name after the update, please go to Web Station > Web Service Portal to delete the virtual host.<br><br>Please note that KodBox will retain its http group's access privileges after its folder is moved. If a user needs to access KodBox's folder, make sure to add their user account to the http group."
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

