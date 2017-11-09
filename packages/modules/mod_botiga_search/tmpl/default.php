<?php

/**
* @version		$Id: mod_botiga_menu  Kim $
* @package		mod_botigamenu v 1.0.0
* @copyright		Copyright (C) 2014 Afi. All rights reserved.
* @license		GNU/GPL, see LICENSE.txt
*/

// restricted access
defined('_JEXEC') or die('Restricted access');
$class_sfx	= htmlspecialchars($params->get('moduleclass_sfx'));

?>

<div class="searchbar <?= $class_sfx; ?>">
	<div class="row">
        <div class="col-md-12">
        	<form action="index.php" method="get">
			<input type="hidden" name="option" value="com_botiga">
			<input type="hidden" name="view" value="search">
            <div id="custom-search-input">
                <div class="input-group col-md-12">
                    <input type="text" name="filter_search" class="form-control" placeholder="Buscar..." />
                    <span class="input-group-btn">
                        <button class="btn btn-info" type="button">
                            <i class="glyphicon glyphicon-search"></i>
                        </button>
                    </span>
                </div>
            </div>
            </form>
        </div>
	</div>
</div>
