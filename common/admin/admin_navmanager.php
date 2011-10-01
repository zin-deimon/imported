<?php
/*
 * $Date$
 * $Revision$
 * $HeadURL$
 */

require_once('common/admin/admin_menu.php');

$page = new Page();
$page->setAdmin();
$page->setTitle('Administration - Navigation - Top Navigation');

if ($_GET['incPrio'])
{
    increasePriority($_GET['incPrio']);
}
elseif ($_GET['decPrio'])
{
    decreasePriority($_GET['decPrio']);
}
elseif ($_POST['new'])
{
    newPage($_POST['name'], $_POST['url'], $_POST['target']);
}
elseif ($_POST['name'])
{
    renamePage($_POST['id'], $_POST['name']);
}
elseif ($_POST['target'])
{
    changeTarget($_POST['id'], $_POST['target']);
}
elseif ($_POST['newUrl'])
{
    changeUrl($_POST['id'], $_POST['newUrl']);
}
elseif ($_POST['delete'])
{
    delPage($_POST['id']);
}
elseif ($_POST['syncStat'])
{
    repairStatLink();
}
elseif ($_POST['hide'])
{
	chgHideStatus($_POST['hide'],1);
}
elseif ($_POST['show'])
{
	chgHideStatus($_POST['show'],0);
}

$html .= '<div class="block-header2">Navigation for intern pages</div>';
$qry = DBFactory::getDBQuery(true);
$query = "select * from kb3_navigation WHERE intern = 1 AND KBSITE = '".KB_SITE."' AND descr <> 'About';";
$result = $qry->execute($query);

if ($result)
{
    $html .= '<table class="kb-table">';
    $html .= "<tr><td width='100'><u><b>Page</b></u></td><th colspan='2'><u>Actions</u></th><th>Hidden</th></tr>";
    $odd = false;
    while ($row = $qry->getRow())
    {
        $html .= "<tr class='$class'><td>".$row['descr']."</td><form action ='?a=admin_navmanager' method='post'><td><input name='name' type='text' value='".$row['descr']."' /></td><td><input type='hidden' name='id' value='".$row['ID']."' /><input type='submit' value='rename' /></td></form>";
        if ($row['hidden']==0){
	        $html .= "<form action ='?a=admin_navmanager' method='post'><td><input type='hidden' name='hide' value='".$row['ID']."' /><input type='submit' value='hide' /></td></form>";
        }else{
    	    $html .= "<form action ='?a=admin_navmanager' method='post'><td><input type='hidden' name='show' value='".$row['ID']."' /><input type='submit' value='show' /></td></form>";
        }
        $html .= "</tr>";
        if (!$odd)
        {
            $odd = true;
            $class = 'kb-table-row-odd';
        }
        else
        {
            $odd = false;
            $class = 'kb-table-row-even';
        }
    }
    $html .= "<tr class='$class'>";
    if (!$odd)
    {
        $odd = true;
        $class = 'kb-table-row-odd';
    }
    else
    {
        $odd = false;
        $class = 'kb-table-row-even';
    }
    $html .= "</table>";
}
$html .= "<div class='block-header2'>Navigation for extern pages</div>";
$qry = DBFactory::getDBQuery(true);
$query = "select * from kb3_navigation WHERE intern = 0 AND KBSITE = '".KB_SITE."';";
$result = $qry->execute($query);

if ($result)
{
    $html .= "<table class='kb-table'>";
    $html .= "<tr><td width='100'><u><b>Page</b></u></td><th colspan='2'>Rename</th><th colspan='2'>URL</th><th colspan='2'>Target</th></tr>";
    $odd = false;
    while ($row = $qry->getRow())
    {
        $html .= "<tr class='$class'><td>".$row['descr']."</td>";
        $html .= "<form action ='?a=admin_navmanager' method='post'><td><input name='name' type='text' value='".$row['descr']."' /></td><td><input type='hidden' name='id' value='".$row['ID']."' /><input type='submit' value='rename'></td></form>";
        $html .= "<form action ='?a=admin_navmanager' method='post'><td><input name='newUrl' type='text' value='".$row['url']."' /></td><td><input type='hidden' name='id' value='".$row['ID']."' /><input type='submit' value='change'></td></form>";
        $html .= "<form action ='?a=admin_navmanager' method='post'><td><select name='target'>";
        $html .= "<option value='_self' ";
        if ($row['target'] == '_self')
        {
            $html .= "selected";
        }
        $html .= ">_self</option>";
        $html .= "<option value='_blank' ";
        if ($row['target'] == '_blank')
        {
            $html .= "selected";
        }
        $html .= ">_blank</option></select>";
        $html .= "</td><td><input type='hidden' name='id' value='".$row['ID']."' /><input type='submit' value='change' /></td></form>";
        $html .= "<form action ='?a=admin_navmanager' method='post'><td><input name='delete' type='hidden' value='1' /><input type='hidden' name='id' value='".$row['ID']."' /><input type='submit' value='delete' /></td></form>";
        $html .= "</tr>";
        if (!$odd)
        {
            $odd = true;
            $class = 'kb-table-row-odd';
        }
        else
        {
            $odd = false;
            $class = 'kb-table-row-even';
        }
    }
    $html .= "<tr class='$class'><td colspan='10'><b><u>New Page:</b></u></td></tr>";
    if (!$odd)
    {
        $odd = true;
        $class = 'kb-table-row-odd';
    }
    else
    {
        $odd = false;
        $class = 'kb-table-row-even';
    }
    $html .= "<tr class='$class'><td>Description:</td>";
    $html .= "<form action ='?a=admin_navmanager' method='post'><td><input name='name' type='text'></td>";
    $html .= "<td>URL:</td><td><input name='url' type='text' value='http://' /></td><td>Target:</td>";
    $html .= "<td><select name='target'><option value='_self'>_self</option><option value='_blank'>_blank</option></select>";
    $html .= "</td><td><input type='hidden' name='new' value='1' /><input type='submit' value='add' /></td></form>";
    $html .= "</tr>";
    $html .= "</table>";
}

$html .= "<div class='block-header2'>Order of the pages in Top Navigation Bar</div>";
$qry = DBFactory::getDBQuery(true);
$query = "select * from kb3_navigation WHERE nav_type = 'top' AND KBSITE = '".KB_SITE."' ORDER BY posnr ;";
$result = $qry->execute($query);

if ($result)
{
    $html .= "<table class='kb-table'>";
    $html .= "<tr><th><u>Nr</u></th><td><u><b>Page</b></u></td><th colspan='2'><u>Actions</u></th></tr>";
    $odd = false;
    while ($row = $qry->getRow())
    {
        if (!$odd)
        {
            $odd = true;
            $class = 'kb-table-row-odd';
        }
        else
        {
            $odd = false;
            $class = 'kb-table-row-even';
        }
        $html .= "<tr class='$class'><td align='right'>".$row['posnr']."</td><td>".$row['descr']."</td>";
        $html .= "<td><a href='?a=admin_navmanager&decPrio=".$row['ID']."'><b> move up </b></a></td>";
        $html .= "<td><a href='?a=admin_navmanager&incPrio=".$row['ID']."'><b> down </b></a></td>";
        $html .= "<tr>" ;
    }
    $html .= "</table>";
}
$page->addContext($menubox->generate());
$page->setContent($html);
$page->generate();

function increasePriority($id)
{
	$id = (int) $id;
    $qry = DBFactory::getDBQuery(true);
	$qry->autocommit(false);
    $query = "SELECT posnr FROM kb3_navigation WHERE ID = $id AND KBSITE = '".KB_SITE."'";
    $qry->execute($query);
    $row = $qry->getRow();
    $next = $row['posnr'] + 1;

    $query = "UPDATE kb3_navigation SET posnr = (posnr-1) WHERE nav_type = 'top' AND posnr = $next AND KBSITE = '".KB_SITE."'";
    $qry->execute($query);

    $query = "UPDATE kb3_navigation SET posnr = (posnr+1) WHERE ID = $id AND KBSITE = '".KB_SITE."'";
    $qry->execute($query);
	$qry->autocommit(true);
}
function decreasePriority($id)
{
	$id = (int) $id;
    $qry = DBFactory::getDBQuery(true);
	$qry->autocommit(false);
    $query = "SELECT posnr FROM kb3_navigation WHERE ID = $id AND KBSITE = '".KB_SITE."'";
    $qry->execute($query);
    $row = $qry->getRow();
    $prev = $row['posnr']-1;

    $query = "UPDATE kb3_navigation SET posnr = (posnr+1) WHERE nav_type = 'top' AND posnr = $prev AND KBSITE = '".KB_SITE."'";
    $qry->execute($query);

    $query = "UPDATE kb3_navigation SET posnr = (posnr-1) WHERE ID = $id AND KBSITE = '".KB_SITE."'";;
    $qry->execute($query);
	$qry->autocommit(true);
}

function renamePage($id, $name)
{
	$id = (int) $id;
    $qry = DBFactory::getDBQuery(true);
	$name = $qry->escape($name);
    $query = "UPDATE kb3_navigation SET descr ='$name' WHERE ID=$id AND KBSITE = '".KB_SITE."'";
    $qry->execute($query);
}

function changeUrl($id, $url)
{
	$id = (int) $id;
    $qry = DBFactory::getDBQuery(true);
	$url = $qry->escape($url);
    $query = "UPDATE kb3_navigation SET url ='$url' WHERE ID=$id AND KBSITE = '".KB_SITE."'";
    $qry->execute($query);
}
function changeTarget($id, $target)
{
	$id = (int) $id;
    $qry = DBFactory::getDBQuery(true);
	$target = $qry->escape($target);
    $query = "UPDATE kb3_navigation SET target ='$target' WHERE ID=$id AND KBSITE = '".KB_SITE."'";
    $qry->execute($query);
}
function newPage($descr, $url, $target)
{
    $qry = DBFactory::getDBQuery(true);
	$descr = $qry->escape($descr);
	$url = $qry->escape($url);
	$target = $qry->escape($target);
    $query = "SELECT max(posnr) as nr FROM kb3_navigation WHERE nav_type='top' AND KBSITE = '".KB_SITE."'";
    $qry->execute($query);
    $row = $qry->getRow();
    $posnr = $row['nr'] + 1;
    $query = "INSERT INTO kb3_navigation SET descr='$descr', intern=0, nav_type='top',url='$url', target ='$target', posnr=$posnr, page='ALL_PAGES', KBSITE = '".KB_SITE."'";
    $qry->execute($query);
}
function delPage($id)
{
	$id = (int) $id;
    $qry = DBFactory::getDBQuery(true);
    $query = "DELETE FROM kb3_navigation WHERE ID=$id AND KBSITE = '".KB_SITE."'";
    $qry->execute($query);
}
function chgHideStatus($id,$status)
{
	$id = (int) $id;
	$status = (int) $status % 2;
    $qry = DBFactory::getDBQuery(true);
    $query = "UPDATE kb3_navigation SET hidden ='$status' WHERE ID=$id AND KBSITE = '".KB_SITE."'";
    $qry->execute($query);
}