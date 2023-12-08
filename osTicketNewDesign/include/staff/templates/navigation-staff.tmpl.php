<?php
if($nav && ($tabs=$nav->getTabs()) && is_array($tabs)){
    foreach ($tabs as $name => $tab) {
        if ($tab['href'][0] != '/')
            $tab['href'] = ROOT_PATH . 'scp/' . $tab['href'];
        echo sprintf('<li class="%s dropdown tickets-menu-btn no-pjax"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">%s<span class="caret"></span></a>',
                //$tab['active'] ? 'active':'inactive',
                @$tab['class'] ?: '',
                //$tab['href'],$tab['desc']);
                $tab['desc']);

        if ($name == "tickets") {

if (!$nav || !($subnav=$nav->getSubMenu()) || !is_array($subnav))
    return;

$activeMenu=$nav->getActiveMenu();
if ($activeMenu>0 && !isset($subnav[$activeMenu-1]))
    $activeMenu=0;

$info = $nav->getSubNavInfo();
            ?>
                    <ul class="dropdown-menu tickets-menu" id="sub_nav">
                        <?php
                        //echo '<ul class="dropdown-menu" id="sub_nav">';
                        //$subnav = $nav->getSubMenu();

                        foreach ($subnav as $k => $item) {
                            if (is_callable($item)) {
                                if ($item($nav) && !$activeMenu)
                                    $activeMenu = 'x';
                                continue;
                            }
                            if ($item['droponly'])
                                continue;
                            $class = $item['iconclass'];
                            if ($activeMenu && $k + 1 == $activeMenu
                                    or ( !$activeMenu && (strpos(strtoupper($item['href']), strtoupper(basename($_SERVER['SCRIPT_NAME']))) !== false
                                    or ( $item['urls'] && in_array(basename($_SERVER['SCRIPT_NAME']), $item['urls'])
                                    )
                                    )))
                                $class = "$class active";
                            if (!($id = $item['id']))
                                $id = "subnav$k";

                            //Extra attributes
                            $attr = '';
                            if ($item['attr'])
                                foreach ($item['attr'] as $name => $value)
                                    $attr .= sprintf("%s='%s' ", $name, $value);
                            if($item['iconclass'] == 'newTicket') {
                            echo sprintf('<li><a class="%s" href="%s" title="%s" id="%s" %s>%s</a></li>', $class, $item['href'], $item['title'] ?? null, $id ?? null, $attr, $item['desc']);
                            } else {
                             echo sprintf('<li><a class="only-ticket Ticket active" href="%s" title="%s" id="%s" %s>%s</a></li>', 'tickets.php', 'Tickets', $id, $attr, 'Tickets');
                             echo sprintf('<li><a id="new-ticket" class="newTicket only-ticket Ticket active" href="%s" title="%s" id="%s" %s>%s</a></li>', 'tickets.php?a=open', 'New Ticket', $id, $attr, 'New Ticket');
                              break;
                            }
                        }
                        echo "</ul>";
                    } else {
                        if ($subnav = $nav->getSubMenu($name)) {
                            echo '<ul class="dropdown-menu">';
                            $links = array();
                            foreach ($subnav as $k => $item) {
                                if (!($id = $item['id']))
                                    $id = "nav$k";
                                //if ($item['href'][0] != '/')
                                //$item['href'] = ROOT_PATH . 'scp/' . $item['href'];                
                                if (!in_array($item['href'], $links)) {
                                    echo sprintf(
                                            '<li><a class="%s" href="%s" title="%s" id="%s">%s</a></li>', $item['iconclass'], $item['href'], $item['title'] ?? null, $id ?? null, $item['desc']);
                                    $links[] = $item['href'];
                                }
                            }
                            empty($links);
                            echo "</ul>";
                        }
                    }
                    echo "</li>";
                }
            }
            ?>
