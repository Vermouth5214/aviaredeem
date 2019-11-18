<!-- sidebar menu -->
<?php
	$segment =  Request::segment(2);
    $sub_segment =  Request::segment(3);
?>
<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <?php 
        if (($userinfo['priv'] == "VSUPER") || ($userinfo['priv'] == "VSUPERT") || ($userinfo['priv'] == "ADMIN")):
    ?>
    <div class="menu_section">
        <h3>GENERAL</h3>
		<ul class="nav side-menu">
			<li class="{{ ($segment == 'dashboard' ? 'active' : '') }}">
				<a href="<?=url('backend/dashboard');?>"><i class="fa fa-dashboard"></i> Dashboard</a>
			</li>
        </ul>
    </div>
    <?php
        endif;
    ?>
    <?php
        // ADMIN //
        if ((($userinfo['priv'] == "VSUPER") || ($userinfo['priv'] == "VSUPERT") || ($userinfo['priv'] == "VREDEEM")) && ($userinfo['uname'] != "mkt01")):
    ?>
    <div class="menu_section">
        <h3>MASTER</h3>
		<ul class="nav side-menu">
            <li class="{{ ($segment == 'campaign' ? 'active' : '') }}">
                <a href="<?=url('backend/campaign');?>"><i class="fa fa-book"></i> Master Campaign</a>
            </li>
                <?php
                //ADMIN //
                if (($userinfo['priv'] == "VSUPER") || ($userinfo['priv'] == "VSUPERT")):
            ?>
                <li class="{{ ($segment == 'master-omzet' ? 'active' : '') }}">
                    <a href="<?=url('backend/master-omzet');?>"><i class="fa fa-money"></i> Master Customer Omzet</a>
                </li>
                <li class="{{ ($segment == 'last-tto' ? 'active' : '') }}">
                    <a href="<?=url('backend/last-tto');?>"><i class="fa fa-list-alt"></i> Last TTO / TTP Number</a>
                </li>
                <li class="{{ ($segment == 'delete-redeem' ? 'active' : '') }}">
                    <a href="<?=url('backend/delete-redeem');?>"><i class="fa fa-trash-o"></i> Delete Redeem</a>
                </li>
            <?php
                endif;
            ?>

        </ul>
    </div>
    <?php
        endif;
    ?>
    <?php
        // AGEN //
        if (($userinfo['priv'] == "VSUPER") || ($userinfo['priv'] == "RECV")):
    ?>
    <div class="menu_section">
        <h3>REDEEM</h3>
		<ul class="nav side-menu">
            <li class="{{ ($segment == 'redeem-hadiah' ? 'active' : '') }}">
                <a href="<?=url('backend/redeem-hadiah');?>"><i class="fa fa-gift"></i> Redeem Hadiah</a>
            </li>
			<li class="{{ ($segment == 'user-guide' ? 'active' : '') }}">
				<a href="<?=url('backend/user-guide');?>" target="_blank"><i class="fa fa-question"></i> User Guide</a>
			</li>
        </ul>
    </div>
    <?php
        endif;
    ?>
    <?php
        if (($userinfo['priv'] == "VSUPER") || ($userinfo['priv'] == "VSUPERT") || ($userinfo['priv'] == "ADMIN")):
    ?>
    <div class="menu_section">
        <h3>REPORT</h3>
		<ul class="nav side-menu">
            <li class="{{ ($segment == 'general-report' ? 'active' : '') }}">
                <a href="<?=url('backend/general-report');?>"><i class="fa fa-file-text-o"></i> General Report</a>
            </li>
        </ul>
    </div>
    <?php
        endif;
    ?>
    <?php
        // SUPER ADMIN //
        if ($userinfo['priv'] == "VSUPER"):
    ?>
	<div class="menu_section">
        <h3>GENERAL</h3>
        <ul class="nav side-menu">
            <li class="{{ ($segment == 'setting' ? 'active' : '') }}">
                <a href="<?=url('backend/setting');?>"><i class="fa fa-cog"></i> Setting</a>
            </li>
        </ul>
    </div>
    <?php
        endif;
    ?>
</div>

