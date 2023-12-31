<?php
/*********************************************************************
    directory.php

    Staff directory

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('staff.inc.php');
$page='directory.inc.php';
$nav->setTabActive('dashboard');
$ost->addExtraHeader('<meta name="tip-namespace" content="dashboard.staff_directory" />',
    "$('#content').data('tipNamespace', 'dashboard.staff_directory');");
require(STAFFINC_DIR.'header-staff.inc.php');
require(STAFFINC_DIR.$page);
include(STAFFINC_DIR.'footer-staff.inc.php');
?>
