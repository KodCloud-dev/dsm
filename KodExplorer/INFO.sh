#!/bin/bash
# Copyright (c) 2015-2023 KodCloud Inc. All rights reserved.

package="KodExplorer"

. "/pkgscripts/include/pkg_util.sh"
version="4.51-602309"
instuninst_restart_services="nginx"
startstop_restart_services="nginx"
firmware="6.0-7300"
os_min_ver="6.1-14715"
os_max_ver="7.0-40000"
install_dep_packages="WebStation>=2.1.9-0153:PHP7.3>=7.3.9-0006"
displayname="KodExplorer"
displayname_enu="KodExplorer" 
displayname_cht="可道雲KodExplorer"
displayname_chs="可道云KodExplorer"
dsmappname="SYNO.SDS.KodExplorer"
thirdparty="yes"
silent_upgrade="yes"
arch="noarch"
reloadui="yes"
adminprotocol="http"
adminport="80"
adminurl="kodexplorer"
dsmuidir="ui"
description="KodExplorer是可道云经典版本, 是一个开源的基于Web的在线文件管理、代码编辑器。此套件可让您在 DiskStation 上快速完成私有云/私有网盘/在线文档管理系统的部署和搭建。"
description_enu="KodExplorer is an open source web-based file management, code editor. The package enables you to quickly complete the deployment and setup of a private cloud / private cloud disk / online document management system on your DiskStation."
description_cht="KodExplorer是可道雲經典版本, 是一個開源的基於Web的線上文件管理、程式碼編輯器。此套件可讓您在DiskStation上快速完成私有雲/私有網盤/線上檔案管理系統的部署和搭建。"
description_chs="KodExplorer是可道云经典版本, 是一个开源的基于Web的在线文件管理、代码编辑器。此套件可让您在 DiskStation 上快速完成私有云/私有网盘/在线文档管理系统的部署和搭建。"
maintainer="KodCloud"
maintainer_url="https://kodcloud.com/"
support_url="https://bbs.kodcloud.com/"
helpurl="https://bbs.kodcloud.com/"

[ "$(caller)" != "0 NULL" ] && return 0
pkg_dump_info


