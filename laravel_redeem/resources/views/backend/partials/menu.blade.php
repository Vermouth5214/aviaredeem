<!-- sidebar menu -->
<?php
	$segment =  Request::segment(2);
	$sub_segment =  Request::segment(3);
?>
<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
        <h3>REDEEM</h3>
		<ul class="nav side-menu">
			<li class="{{ ($segment == 'dashboard' ? 'active' : '') }}">
				<a href="<?=url('backend/dashboard');?>"><i class="fa fa-dashboard"></i> Dashboard</a>
			</li>
        </ul>
    </div>

    <?php
        // ADMIN //
        if (($userinfo['priv'] == "VSUPER") || ($userinfo['priv'] == "VSUPERT") || ($userinfo['priv'] == "VREDEEM")):
    ?>
    <div class="menu_section">
        <h3>MASTER</h3>
		<ul class="nav side-menu">
            <?php
                //ADMIN //
                if (($userinfo['priv'] == "VSUPER") || ($userinfo['priv'] == "VSUPERT")):
            ?>
                <li class="{{ ($segment == 'master-omzet' ? 'active' : '') }}">
                    <a href="<?=url('backend/master-omzet');?>"><i class="fa fa-money"></i> Master Customer Omzet</a>
                </li>
            <?php
                endif;
            ?>
                <li class="{{ ($segment == 'campaign' ? 'active' : '') }}">
                    <a href="<?=url('backend/campaign');?>"><i class="fa fa-book"></i> Master Campaign</a>
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

