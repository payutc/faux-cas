<?php
/*
 *  Copyright (C) 2013 payutc <payutc@assos.utc.fr>
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

session_start();
function gen_ticket_and_redirect($url) {
  $ticket = $_SESSION['login'] . '@' . $url . '@' .rand(1, 1000);
  $_SESSION[$url]['ticket'] = $ticket;
  
  $url_infos = parse_url($url);
  if (isset($url_infos['query']))
    $redirect = $url . '&ticket=' . $ticket;
  else
    $redirect = $url . '?ticket=' . $ticket;
  header('Location: ' . $redirect);
}

$action = null;
if (!isset($_GET['action'])) {
    $r = explode('?', $_SERVER['REQUEST_URI']);
    $r = $r[0];
    $r = explode('/', $r);
    $action = $r[1];
}
else {
    $action = $_GET['action'];
}

switch ($action) {
case 'login':
  $service = $_GET['service'];
  if (isset($_SESSION['state']) AND $_SESSION['state'] === 'ok') {
    gen_ticket_and_redirect($service);
  } else {
    include 'form.html';
  }
  break;
case 'auth':
  $service = $_GET['service'];
  $_SESSION['login'] = $_GET['login'];
  gen_ticket_and_redirect($service);
  break;
case 'serviceValidate':
  $service = $_GET['service'];
  $ticket = $_GET['ticket'];
  $tab = explode("@", $ticket);
  if ($service == $tab[1]) {
    $user = $tab[0];
    $_SESSION['state'] = 'ok';
    include 'success.xml';
  } else {
    include 'fail.xml';
  }
  break;
case 'logout':
  unset($_SESSION['state']);
  unset($_SESSION['login']);
  $url = $_GET['url'];
  header("Location: $url");
  break;
default:
  die('Not a valid action !');
}

?>
