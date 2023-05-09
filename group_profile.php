<?php
#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|
#|                                                                        #|
#|         Copyright © 2014-2023 - MyHabbo Tout droits réservés.          #|
#|																		  #|
#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|

include("./config.php");
$pageid = "forum";
$pagename = "Forum";

// Search function
if (isset($_POST['searchString'])) {
	$searchString = FilterText($_POST['searchString']);
	$stmt = $bdd->prepare("SELECT id FROM guilds WHERE name LIKE ? LIMIT 1");
	$stmt->execute(array('%' . $searchString . '%'));
	$check = $stmt->fetch(PDO::FETCH_ASSOC);
	$found = $stmt->rowCount();
	if ($found > 0) {
		header("Location: group_profile?id=" . $check['id']);
		exit;
	}
}



if (isset($_GET['id']) && is_numeric($_GET['id'])) {
	$id = $_GET['id'];
	$stmt = $bdd->prepare("SELECT * FROM guilds WHERE id=? LIMIT 1");
	$stmt->execute(array($id));
	$groupdata = $stmt->fetch(PDO::FETCH_ASSOC);
	$exists = $stmt->rowCount();

	if ($exists > 0) {
		$groupid = $groupdata['id'];
		$error = false;
		$pagename = $groupdata['name'];
		$ownerid = $groupdata['ownerid'];

		$stmt = $bdd->prepare("SELECT COUNT(*) FROM guilds_members WHERE guild_id=? AND is_pending='0'");
		$stmt->execute(array($groupid));
		$members = $stmt->fetchColumn();

		$stmt = $bdd->prepare("SELECT * FROM guilds_members WHERE user_id=? AND guild_id=? AND is_pending='0' LIMIT 1");
		$stmt->execute(array($my_id, $groupid));
		$is_member = $stmt->rowCount();

		if ($is_member > 0 && $logged_in) {
			$is_member = true;
			$my_membership = $stmt->fetch(PDO::FETCH_ASSOC);
			$member_rank = $my_membership['member_rank'];
		} else {
			$is_member = false;
		}
	} else {
		$error = true;
	}
} else {
	$error = true;
}


if (isset($_GET['do']) && $_GET['do'] == "edit" && $logged_in) {
	if ($is_member && $member_rank > 1) {
		$edit_mode = true;

		$stmt = $bdd->prepare("SELECT * FROM cms_homes_group_linker WHERE userid=? LIMIT 1");
		$stmt->execute([$my_id]);
		$linkers = $stmt->rowCount();

		if ($linkers > 0) {
			$stmt = $bdd->prepare("UPDATE cms_homes_group_linker SET active='1', groupid=? WHERE userid=? LIMIT 1");
			$stmt->execute([$groupid, $my_id]);
		} else {
			$stmt = $bdd->prepare("INSERT INTO cms_homes_group_linker (userid,groupid,active) VALUES (?,?,?)");
			$active = 1;
			$stmt->execute([$my_id, $groupid, $active]);
		}
	} else {
		header("location:group_profile.php?do=bounce&id=" . $groupid . "");
		$edit_mode = false;
	}
} else {
	$edit_mode = false;
}



if (!$error) {

	$body_id = "viewmode";

	if ($edit_mode) {

		$body_id = "editmode";
	}
} else {

	$body_id = "home";
}

if ($groupdata['type'] !== "1" && $is_member !== true) {
	// If the group type is NOT exclusive/moderated, we have to delete any pending requests
	// this user has, simply because there's no longer need to put the user in the waiting list.
	$stmt = $bdd->prepare("DELETE FROM guilds_members WHERE is_pending='1' AND user_id=? AND guild_id=? LIMIT 1");
	$stmt->bind_param('ii', $my_id, $groupid);
	$stmt->execute();
}


$viewtools = "	<div class=\"myhabbo-view-tools\">\n";

if ($logged_in && !$is_member && $groupdata['type'] !== "2" && $my_membership['is_pending'] !== "1") {
	$viewtools = $viewtools . "<a href=\"joingroup.php?groupId=" . $groupid . "\" id=\"join-group-button\">";
	if ($groupdata['type'] == "0" || $groupdata['type'] == "3") {
		$viewtools = $viewtools . "Rejoindre";
	} else {
		$viewtools = $viewtools . "Demander une adh&eacute;sion";
	}
	$viewtools = $viewtools . "</a>";
}
if ($logged_in && $my_membership['is_current'] !== "1" && $is_member) {
	$viewtools = $viewtools . "<a href=\"#\" id=\"select-favorite-button\">Mettre en favoris</a>\n";
}
if ($logged_in && $my_membership['is_current'] == "1" && $is_member) {
	$viewtools = $viewtools . "<a href=\"#\" id=\"deselect-favorite-button\">Enlever des favoris</a>";
}
if ($logged_in && $is_member && $my_id !== $ownerid) {
	$viewtools = $viewtools . "<a href=\"leavegroup.php?groupId=" . $groupid . "\" id=\"leave-group-button\">Quitter le clan</a>\n";
}

$viewtools = $viewtools . "	</div>\n";


$bg_fetch = $bdd->prepare("SELECT data FROM cms_homes_stickers WHERE type=:type AND groupid=:groupid LIMIT 1");
$bg_fetch->execute(array(':type' => '4', ':groupid' => $groupid));
$bg_exists = $bg_fetch->rowCount();


if ($bg_exists < 1) { // if there's no background override for this user set it to the standard
	$bg = "b_bg_pattern_abstract2";
} else {
	$bg_fetch = $bdd->prepare("SELECT bg FROM cms_homes_backgrounds WHERE userid = ? LIMIT 1");
	$bg_fetch->bind_param('i', $my_id);
	$bg_fetch->execute();
	$result = $bg_fetch->get_result();
	$bg_exists = $result->rowCount();

	if ($bg_exists > 0) {
		$bg = $result->fetch_row()[0];
		$bg = "b_" . $bg;
	} else {
		$bg = "b_bg_pattern_abstract2";
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<title><?PHP echo $sitename; ?> &raquo; <?PHP echo $pagename; ?></title>

	<script type="text/javascript">
		var andSoItBegins = (new Date()).getTime();
		var ad_keywords = "";
		document.habboLoggedIn = true;
		var habboName = "<?PHP echo $user['username']; ?>";
		var habboReqPath = "<?PHP echo $url; ?>";
		var habboStaticFilePath = "<?PHP echo $imagepath; ?>";
		var habboImagerUrl = "<?php echo $avatarimage ?>";
		var habboPartner = "";
		var habboDefaultClientPopupUrl = "<?PHP echo $url; ?>/client";
		window.name = "habboMain";
		if (typeof HabboClient != "undefined") {
			HabboClient.windowName = "uberClientWnd";
		}
	</script>

	<link rel="shortcut icon" href="<?PHP echo $imagepath; ?>favicon.ico" type="image/vnd.microsoft.icon" />
	<script src="<?PHP echo $imagepath; ?>static/js/libs2.js" type="text/javascript"></script>
	<script src="<?PHP echo $imagepath; ?>static/js/visual.js" type="text/javascript"></script>
	<script src="<?PHP echo $imagepath; ?>static/js/libs.js" type="text/javascript"></script>
	<script src="<?PHP echo $imagepath; ?>static/js/common.js" type="text/javascript"></script>
	<script src="<?PHP echo $imagepath; ?>js/tooltip.js" type="text/javascript"></script>

	<script src="<?PHP echo $imagepath; ?>static/js/fullcontent.js" type="text/javascript"></script>
	<link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/style.css<?php echo '?' . mt_rand(); ?>" type="text/css" />
	<link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/buttons.css<?php echo '?' . mt_rand(); ?>" type="text/css" />
	<link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/boxes.css<?php echo '?' . mt_rand(); ?>" type="text/css" />
	<link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/tooltips.css<?php echo '?' . mt_rand(); ?>" type="text/css" />
	<link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/personal.css<?php echo '?' . mt_rand(); ?>" type="text/css" />
	<script src="<?PHP echo $imagepath; ?>static/js/habboclub.js" type="text/javascript"></script>
	<link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/minimail.css<?php echo '?' . mt_rand(); ?>" type="text/css" />
	<link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/myhabbo/control.textarea.css<?php echo '?' . mt_rand(); ?>" type="text/css" />
	<script src="<?PHP echo $imagepath; ?>static/js/minimail.js" type="text/javascript"></script>



	<meta name="description" content="<?PHP echo $description; ?>" />
	<meta name="keywords" content="<?PHP echo $keyword; ?>" />
	<!--[if IE 8]>
<link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/ie8.css<?php echo '?' . mt_rand(); ?>" type="text/css" />
<![endif]-->
	<!--[if lt IE 8]>
<link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/ie.css<?php echo '?' . mt_rand(); ?>" type="text/css" />
<![endif]-->
	<!--[if lt IE 7]>
<link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/ie6.css<?php echo '?' . mt_rand(); ?>" type="text/css" />
<script src="<?PHP echo $imagepath; ?>static/js/pngfix.js" type="text/javascript"></script>
<script type="text/javascript">
try { document.execCommand('BackgroundImageCache', false, true); } catch(e) {}
</script>
 
<style type="text/css">
body { behavior: url(http://www.habbo.com/js/csshover.htc); }
</style>
<![endif]-->
	<meta name="build" content="<?PHP echo $build; ?> >> <?PHP echo $version; ?>" />
</head>

<?php
if (!$error) {
	$stmt = $con->prepare("UPDATE guilds SET views=views+1 WHERE id=? LIMIT 1");
	$stmt->bind_param('i', $groupid);
	$stmt->execute();
?>


	<div id="container">
		<div id="content" style="position: relative" class="clearfix">
			<div id="mypage-wrapper" class="cbb blue">
				<div class="box-tabs-container box-tabs-left clearfix">
					<?php if ($member_rank > 1 && !$edit_mode) { ?><a href="#" id="myhabbo-group-tools-button" class="new-button dark-button edit-icon" style="float:left"><b><span></span>Editer</b><i></i></a><?php } ?>
					<?php if (!$edit_mode) {
						echo $viewtools;
					} ?>
					<h2 class="page-owner">
						<?php echo HoloText($groupdata['name']); ?>&nbsp;
						<?php if ($groupdata['type'] == "2") { ?><img src='./web-gallery/images/status_closed_big.gif' alt='Closed Group' title='Closed Group'><?php } ?>
						<?php if ($groupdata['type'] == "1") { ?><img src='./web-gallery/images/status_exclusive_big.gif' alt='Moderated Group' title='Moderated Group'><?php } ?></h2>
					</h2>
					<ul class="box-tabs">
						<li class="selected"><a href="group_profile.php?id=<?php echo $_GET['id']; ?>">Clan</a><span class="tab-spacer"></span></li>
						<li><a href="group_forum.php?id=<?php echo $_GET['id']; ?>">Discussion du clan <?php if ($groupdata['pane'] > 0) { ?><img src="http://images.habbohotel.nl/habboweb/23_deebb3529e0d9d4e847a31e5f6fb4c5b/9/web-gallery/images/grouptabs/privatekey.png"><?php } ?></a><span class="tab-spacer"></span></li>
					</ul>
				</div>
				<div id="mypage-content">
					<?php if ($edit_mode == true) { ?>
						<div id="top-toolbar" class="clearfix">
							<ul>
								<li><a href="#" id="inventory-button">Inventaire</a></li>
								<li><a href="#" id="webstore-button">Magasin</a></li>
							</ul>

							<form action="#" method="get" style="width: 50%;">
								<a id="cancel-button" class="new-button red-button cancel-icon" href="#"><b><span></span>Quitter</b><i></i></a>
								<a id="save-button" class="new-button green-button save-icon" href="#"><b><span></span>Sauvegarder</b><i></i></a>
							</form>
						</div>
					<?php 	} ?>
					<div id="mypage-bg" class="<?php echo $bg; ?>">
						<div id="playground-outer">
							<div id="playground">

								<?php
								$get_em = $bdd->prepare("SELECT id, type, x, y, z, data, skin, subtype, var FROM cms_homes_stickers WHERE groupid=:groupid AND type < 4 LIMIT 200");
								$get_em->execute(array(':groupid' => $groupid));

								while ($row = $get_em->fetch(PDO::FETCH_NUM)) {

									switch ($row[1]) {
										case 1:
											$type = "sticker";
											break;
										case 2:
											$type = "widget";
											break;
										case 3:
											$type = "stickie";
											break;
										case 4:
											$type = "ignore";
											break;
									}

									if ($edit_mode == true) {
										$edit = "\n<img src=\"./web-gallery/images/myhabbo/icon_edit.gif\" width=\"19\" height=\"18\" class=\"edit-button\" id=\"" . $type . "-" . $row[0] . "-edit\" />
<script language=\"JavaScript\" type=\"text/javascript\">
Event.observe(\"" . $type . "-" . $row[0] . "-edit\", \"click\", function(e) { openEditMenu(e, " . $row[0] . ", \"" . $type . "\", \"" . $type . "-" . $row[0] . "-edit\"); }, false);
</script>\n";
									} else {
										$edit = " ";
									}

									if ($type == "stickie") {
										printf("<div class=\"movable stickie n_skin_%s-c\" style=\" left: %spx; top: %spx; z-index: %s;\" id=\"stickie-%s\">
	<div class=\"n_skin_%s\" >
		<div class=\"stickie-header\">
			<h3>%s</h3>
			<div class=\"clear\"></div>
		</div>
		<div class=\"stickie-body\">
			<div class=\"stickie-content\">
				<div class=\"stickie-markup\">%s</div>
				<div class=\"stickie-footer\">
				</div>
			</div>
		</div>
	</div>
</div>", $row[6], $row[2], $row[3], $row[4], $row[0], $row[6], $edit, bbcode_format(nl2br(HoloText($row[5]))));
									} elseif ($type == "sticker") {
										printf("<div class=\"movable sticker s_%s\" style=\"left: %spx; top: %spx; z-index: %s\" id=\"sticker-%s\">\n%s\n</div>", $row[5], $row[2], $row[3], $row[4], $row[0], $edit);
									} elseif ($type == "widget") {

										switch ($row[7]) {
											case "1":
												$subtype = "Profilewidget";
												break;
											case "3":
												$subtype = "MemberWidget";
												break;
											case "4":
												$subtype = "GuestbookWidget";
												break;
											case "5":
												$subtype = "TraxPlayerWidget";
										}

										if ($subtype == "Profilewidget") {

											$found_profile = true;

											echo "<div class=\"movable widget GroupInfoWidget\" id=\"widget-" . $row[0] . "\" style=\" left: " . $row[2] . "px; top: " . $row[3] . "px; z-index: " . $row[4] . ";\">
<div class=\"w_skin_" . $row[6] . "\">
	<div class=\"widget-corner\" id=\"widget-" . $row[0] . "-handle\">
		<div class=\"widget-headline\"><h3><span class=\"header-left\">&nbsp;</span><span class=\"header-middle\">Informations du clan</span><span class=\"header-right\">" . $edit . "</span></h3>
		</div>
	</div>
	<div class=\"widget-body\">
		<div class=\"widget-content\">";
								?>

											<div class=\"group-info-icon\"><img src='./habbo-imaging/<?php if (!isset($_GET['x'])) {
																											echo "badge-fill/" . $groupdata['badge'] . ".gif";
																										} else {
																											echo "badge.php?badge=" . $groupdata['badge'] . "";
																										} ?>' /></div>
											<?php echo "
<h4>" . HoloText($groupdata['name']) . "</h4>

<p>
Cr&eacute;e le: <strong>" . $groupdata['created'] . "</strong>
</p>

<p>
<strong>" . $members . "</strong> membres
</p>";
											if ($groupdata['roomid'] != 0 || $groupdata['roomid'] != "" || $groupdata['roomid'] != " ") {
												$stmt = $bdd->prepare("SELECT name FROM rooms WHERE id=:id LIMIT 1");
												$stmt->bindParam(':id', $groupdata['roomid'], PDO::PARAM_INT);
												$stmt->execute();
												$roominfo = $stmt->fetch(PDO::FETCH_ASSOC); ?>
												<?php if ($groupdata['roomid'] <> 0) { ?><p><a href="client.php?forwardId=2&amp;roomId=<?php echo $groupdata['roomid']; ?>" onclick="HabboClient.roomForward(this, '<?php echo $groupdata['roomid']; ?>', 'private'); return false;" target="client" class="group-info-room"><?php echo HoloText($roominfo['name']); ?></a></p><?php } ?>
											<?php
											}

											echo "\n<div class=\"group-info-description\">" . HoloText($groupdata['description']) . "</div>

<script type=\"text/javascript\">
    document.observe(\"dom:loaded\", function() {
        new GroupInfoWidget('" . $groupid . "', '" . $row[0] . "');
    });
</script>

		<div class=\"clear\"></div>
		</div>
	</div>
</div>
</div>";
										} elseif ($subtype == "MemberWidget") {
											echo "<div class=\"movable widget MemberWidget\" id=\"widget-" . $row[0] . "\" style=\" left: " . $row[2] . "px; top: " . $row[3] . "px; z-index: " . $row[4] . ";\">
<div class=\"w_skin_" . $row[6] . "\">
	<div class=\"widget-corner\" id=\"widget-" . $row[0] . "-handle\">
		<div class=\"widget-headline\"><h3><span class=\"header-left\">&nbsp;</span><span class=\"header-middle\">Membres de ce clan (<span id=\"avatar-list-size\">" . $members . "</span>)</span><span class=\"header-right\">" . $edit . "</span></h3>
		</div>
	</div>
	<div class=\"widget-body\">
		<div class=\"widget-content\">

<div id=\"avatar-list-search\">
<input type=\"text\" style=\"float:left;\" id=\"avatarlist-search-string\"/>
<a class=\"new-button\" style=\"float:left;\" id=\"avatarlist-search-button\"><b>Chercher</b><i></i></a>
</div>
<br clear=\"all\"/>

<div id=\"avatarlist-content\">\n";

											$bypass = true;
											$widgetid = $row['0'];
											include('./myhabbo/avatarlist_membersearchpaging.php');

											echo "<script type=\"text/javascript\">
document.observe(\"dom:loaded\", function() {
	window.widget" . $row[0] . " = new MemberWidget('" . $groupid . "', '" . $row[0] . "');
});
</script>

</div>
		<div class=\"clear\"></div>
		</div>
	</div>
</div>
</div>";
										} elseif ($subtype == "GuestbookWidget") {
											$stmt = $bdd->prepare("SELECT COUNT(*) FROM cms_guestbook WHERE widget_id=:widget_id");
											$stmt->bindParam(':widget_id', $row['0'], PDO::PARAM_INT);
											$stmt->execute();
											$count = $stmt->fetchColumn();

											if ($row['10'] == "0") {;
												$status = "public";
											} else {
												$status = "private";
											}
											?>
											<div class="movable widget GuestbookWidget" id="widget-<?php echo $row['0']; ?>" style=" left: <?php echo $row['2']; ?>px; top: <?php echo $row['3']; ?>px; z-index: <?php echo $row['4']; ?>;">
												<div class="w_skin_<?php echo $row['6']; ?>">
													<div class="widget-corner" id="widget-<?php echo $row['0']; ?>-handle">
														<div class="widget-headline">
															<h3>
																<?php echo $edit; ?>
																<span class="header-left">&nbsp;</span><span class="header-middle">Mon livre d'or(<span id="guestbook-size"><?php echo $count; ?></span>) <span id="guestbook-type" class="<?php echo $status; ?>"><img src="./web-gallery/images/groups/status_exclusive.gif" title="Friends only" alt="Friends only" /></span></span><span class="header-right">&nbsp;</span>
															</h3>
														</div>
													</div>
													<div class="widget-body">
														<div class="widget-content">
															<div id="guestbook-wrapper" class="gb-public">
																<ul class="guestbook-entries" id="guestbook-entry-container">
																	<?php if ($count == 0) { ?>
																		<div id="guestbook-empty-notes">Aucune entr&eacute;e</div>
																	<?php } else { ?>
																	<?php
																		$stmt = $bdd->prepare("SELECT * FROM guilds WHERE id=:id LIMIT 1");
																		$stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
																		$stmt->execute();
																		$grouprrow = $stmt->fetch(PDO::FETCH_ASSOC);

																		$i = 0;
																		$sql = "SELECT * FROM guestbook_entries WHERE groupid = :groupid ORDER BY id DESC LIMIT :limit";
																		$stmt = $bdd->prepare($sql);
																		$stmt->bindValue(':groupid', $grouprrow['id'], PDO::PARAM_INT);
																		$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
																		$stmt->execute();

																		while ($row1 = $stmt->fetch(PDO::FETCH_ASSOC)) {
																			$i++;

																			$userstmt = $bdd->prepare("SELECT * FROM users WHERE id = :userid LIMIT 1");
																			$userstmt->bindValue(':userid', $row1['userid'], PDO::PARAM_INT);
																			$userstmt->execute();
																			$userrow = $userstmt->fetch(PDO::FETCH_ASSOC);

																			if ($my_id == $row1['userid'] || $grouprrow['ownerid'] == $my_id) {
																				$owneronly = "<img src=\"./web-gallery/images/myhabbo/buttons/delete_entry_button.gif\" id=\"gbentry-delete-" . $row1['id'] . "\" class=\"gbentry-delete\" style=\"cursor:pointer\" alt=\"\"/><br/>";
																			} else {
																				$owneronly = "";
																			}

																			if (IsUserOnline($row1['userid'])) {
																				$useronline = "online";
																			} else {
																				$useronline = "offline";
																			}

																			printf(
																				"	<li id=\"guestbook-entry-%s\" class=\"guestbook-entry\">
					<div class=\"guestbook-author\">
						<img src=\"http://www.habbo.co.uk/habbo-imaging/avatarimage?figure=%s&direction=2&head_direction=2&gesture=sml&size=s\" alt=\"%s\" title=\"%s\"/>
					</div>
					<div class=\"guestbook-actions\">
						%s
					</div>
					<div class=\"guestbook-message\">
						<div class=\"%s\">
							<a href=\"./user_profile.php?id=%s\">%s</a>
						</div>
						<p>%s</p>
					</div>
					<div class=\"guestbook-cleaner\">&nbsp;</div>
					<div class=\"guestbook-entry-footer metadata\">%s</div>
				</li>",
																				$row1['id'],
																				$userrow['figure'],
																				$userrow['name'],
																				$userrow['name'],
																				$useronline,
																				$userrow['id'],
																				$userrow['name'],
																				HoloText($row1['message'], false, true),
																				$userrow['time']
																			);
																		}
																	} ?>
																</ul>
															</div>
															<?php if ($edit_mode == false) { ?>
																<div class="guestbook-toolbar clearfix">
																	<a href="#" class="new-button envelope-icon" id="guestbook-open-dialog">
																		<b><span></span>Post new message</b><i></i>
																	</a>
																</div>
															<?php } ?>
															<script type="text/javascript">
																document.observe("dom:loaded", function() {
																	var gb<?php echo $row['0']; ?> = new GuestbookWidget('17570', '<?php echo $row['0']; ?>', 500);
																	var editMenuSection = $('guestbook-privacy-options');
																	if (editMenuSection) {
																		gb<?php echo $row['0']; ?>.updateOptionsList('public');
																	}
																});
															</script>
															<div class="clear"></div>
														</div>
													</div>
												</div>
											</div>
										<?php
										} elseif ($subtype == "TraxPlayerWidget") {
											$sql = "SELECT * FROM guilds WHERE id = ? LIMIT 1";
											$stmt = $bdd->prepare($sql);
											$stmt->bind_param("i", $_GET['id']);
											$stmt->execute();
											$grouprrow = $stmt->fetch(PDO::FETCH_ASSOC);
										?>
											<div class="movable widget TraxPlayerWidget" id="widget-<?php echo $row['0']; ?>" style=" left: <?php echo $row['2']; ?>px; top: <?php echo $row['3']; ?>px; z-index: <?php echo $row['4']; ?>;">
												<div class="w_skin_<?php echo $row['6']; ?>">
													<div class="widget-corner" id="widget-<?php echo $row['0']; ?>-handle">
														<div class="widget-headline">
															<h3><?php echo $edit; ?><span class="header-left">&nbsp;</span><span class="header-middle">TRAXPLAYER</span><span class="header-right">&nbsp;</span></h3>
														</div>
													</div>
													<div class="widget-body">
														<div class="widget-content">
															<?php
															if ($row['8'] == "") {
																$songselected = false;
															} else {
																$songselected = true;
															}
															if ($edit_mode == true) { ?>
																<div id="traxplayer-content" style="text-align: center;">
																	<img src="./web-gallery/images/traxplayer/player.png" />
																</div>

																<div id="edit-menu-trax-select-temp" style="display:none">
																	<select id="trax-select-options-temp">
																		<option value="">- Choose song -</option>
																		<?php
																		$sql = "SELECT * FROM furniture WHERE ownerid = ?";
																		$stmt = $bdd->prepare($sql);
																		$stmt->bind_param("i", $grouprrow['ownerid']);
																		$stmt->execute();
																		$mysql = $stmt->get_result();

																		$i = 0;
																		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
																			$i++;
																			$sql = "SELECT * FROM soundmachine_songs WHERE machineid = ?";
																			$stmt = $bdd->prepare($sql);
																			$stmt->bind_param("i", $machinerow['id']);
																			$stmt->execute();
																			$result = $stmt->get_result();

																			$n = 0;
																			while ($songrow = $result->fetch_assoc()) {
																				$n++;
																				if ($songrow['id'] <> "") {
																					echo "		<option value=\"" . $songrow['id'] . "\">" . trim(nl2br(HoloText($songrow['title']))) . "</option>\n";
																				}
																			}
																		} ?>
																	</select>

																</div>
															<?php } elseif ($songselected == false) { ?>
																You do not have a selected Trax song.
															<?php } else {
																$sql = "SELECT * FROM soundmachine_songs WHERE id = ? LIMIT 1";
																$stmt = $bdd->prepare($sql);
																$stmt->bind_param("i", $row['8']);
																$stmt->execute();
																$result = $stmt->get_result();
																$songrow1 = $result->fetch_assoc();
															?>
																<div id="traxplayer-content" style="text-align:center;"></div>
																<embed type="application/x-shockwave-flash" src="<?php echo $path; ?>web-gallery/flash/traxplayer/traxplayer.swf" name="traxplayer" quality="high" base="<?php echo $path; ?>web-gallery/flash/traxplayer/" allowscriptaccess="always" menu="false" wmode="transparent" flashvars="songUrl=<?php echo $path; ?>myhabbo/trax_song.php?songId=<?php echo $row['8']; ?>&amp;sampleUrl=http://images.habbohotel.com/dcr/hof_furni//mp3/" height="66" width="210" />
															<?php } ?>

															<div class="clear"></div>
														</div>
													</div>
												</div>
											</div><?php
												}
											}
										}

										if ($found_profile !== true) {

											$sql = "INSERT INTO cms_homes_stickers (userid,groupid,type,subtype,x,y,z,skin) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
											$stmt = $bdd->prepare($sql);
											$stmt->bind_param("iiiiiiis", -1, $groupid, 2, 1, 25, 25, 5, 'defaultskin');
											$stmt->execute();


											echo "<div class=\"movable widget GroupInfoWidget\" id=\"widget-" . $row[0] . "\" style=\" left: 25px; top: 25px; z-index: 5;\">
<div class=\"w_skin_defaultskin\">
	<div class=\"widget-corner\" id=\"widget-1994412-handle\">
		<div class=\"widget-headline\"><h3><span class=\"header-left\">&nbsp;</span><span class=\"header-middle\">Informations du clan</span><span class=\"header-right\">&nbsp;</span></h3>
		</div>
	</div>
	<div class=\"widget-body\">
		<div class=\"widget-content\">

<div class=\"group-info-icon\"><img src='./habbo-imaging/badge-fill/" . $groupdata['badge'] . ".gif' /></div>

<h4>" . HoloText($groupdata['name']) . "</h4>

<p>
Created: <strong>" . $groupdata['created'] . "</strong>
</p>

<p>
<strong>" . $members . "</strong> members
</p>\n";

											// <p><a href=\"http://www.habbo.nl/client?forwardId=2&amp;roomId=13303122\" onclick=\"roomForward(this, '13303122', 'private'); return false;\" target=\"client\" class=\"group-info-room\">The church of bobbaz</a></p>

											echo "\n<div class=\"group-info-description\">" . HoloText($groupdata['description']) . "</div>

<script type=\"text/javascript\">
    document.observe(\"dom:loaded\", function() {
        new GroupInfoWidget('55918', '1478728');
    });
</script>

		<div class=\"clear\"></div>
		</div>
	</div>
</div>
</div>";
										}
													?>
							</div>
						</div>
						<div id="mypage-ad">
							<div class="habblet ">
								<div class="ad-container">
									&nbsp;
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>

			<script language="JavaScript" type="text/javascript">
				initEditToolbar();
				initMovableItems();
				document.observe("dom:loaded", initDraggableDialogs);
			</script>


			<div id="edit-save" style="display:none;"></div>
		</div>
		<div id="footer">
			<p><a href="index.php" target="_self">Accueil</a> | <a href="./disclaimer.php" target="_self">Conditions d'utilisation</a> | <a href="./privacy.php" target="_self">Informations pratiques</a></p>

		</div>
	</div>

	</div>

	<?php if ($edit_mode) { ?>
		<div id="edit-menu" class="menu">
			<div class="menu-header">
				<div class="menu-exit" id="edit-menu-exit"><img src="./web-gallery/images/dialogs/menu-exit.gif" alt="" width="11" height="11" /></div>
				<h3>Editer</h3>
			</div>
			<div class="menu-body">
				<div class="menu-content">
					<form action="#" onsubmit="return false;">
						<div id="edit-menu-skins">
							<select id="edit-menu-skins-select">
								<option value="1" id="edit-menu-skins-select-defaultskin">Default</option>
								<option value="6" id="edit-menu-skins-select-goldenskin">Golden</option>
								<?php if (IsHCMember($my_id)) { ?>
									<option value="8" id="edit-menu-skins-select-hc_pillowskin">HC Bling</option>
									<option value="7" id="edit-menu-skins-select-hc_machineskin">HC Scifi</option>
								<?php } ?>
								<?php if ($user_rank > 5) { ?>
									<option value="9" id="edit-menu-skins-select-nakedskin">Staff - Naked Skin</option>
								<?php } ?>
								<option value="3" id="edit-menu-skins-select-metalskin">Metal</option>
								<option value="5" id="edit-menu-skins-select-notepadskin">Notepad</option>
								<option value="2" id="edit-menu-skins-select-speechbubbleskin">Speech Bubble</option>
								<option value="4" id="edit-menu-skins-select-noteitskin">Stickie Note</option>
							</select>
						</div>
						<div id="edit-menu-stickie">
							<p>Warning! If you click 'Remove', the note will be permanently deleted.</p>
						</div>
						<div id="rating-edit-menu">
							<input type="button" id="ratings-reset-link" value="Reset rating" />
						</div>
						<div id="highscorelist-edit-menu" style="display:none">
							<select id="highscorelist-game">
								<option value="">Select game</option>
								<option value="1">Battle Ball: Rebound!</option>
								<option value="2">SnowStorm</option>
								<option value="0">Wobble Squabble</option>
							</select>
						</div>
						<div id="edit-menu-remove-group-warning">
							<p>This item belongs to another user. If you remove it, it will return to their inventory.</p>
						</div>
						<div id="edit-menu-gb-availability">
							<select id="guestbook-privacy-options">
								<option value="private">Members only</option>
								<option value="public">Public</option>
							</select>
						</div>
						<div id="edit-menu-trax-select">
							<select id="trax-select-options"></select>
						</div>
						<div id="edit-menu-remove">
							<input type="button" id="edit-menu-remove-button" value="Remove" />
						</div>
					</form>
					<div class="clear"></div>
				</div>
			</div>
			<div class="menu-bottom"></div>
		</div>
		<script language="JavaScript" type="text/javascript">
			Event.observe(window, "resize", function() {
				if (editMenuOpen) closeEditMenu();
			}, false);
			Event.observe(document, "click", function() {
				if (editMenuOpen) closeEditMenu();
			}, false);
			Event.observe("edit-menu", "click", Event.stop, false);
			Event.observe("edit-menu-exit", "click", function() {
				closeEditMenu();
			}, false);
			Event.observe("edit-menu-remove-button", "click", handleEditRemove, false);
			Event.observe("edit-menu-skins-select", "click", Event.stop, false);
			Event.observe("edit-menu-skins-select", "change", handleEditSkinChange, false);
			Event.observe("guestbook-privacy-options", "click", Event.stop, false);
			Event.observe("guestbook-privacy-options", "change", handleGuestbookPrivacySettings, false);
			Event.observe("trax-select-options", "click", Event.stop, false);
			Event.observe("trax-select-options", "change", handleTraxplayerTrackChange, false);
		</script>
	<?php } else { ?>
		<div class="cbb topdialog" id="guestbook-form-dialog">
			<h2 class="title dialog-handle">Edit guestbook entry</h2>

			<a class="topdialog-exit" href="#" id="guestbook-form-dialog-exit">X</a>
			<div class="topdialog-body" id="guestbook-form-dialog-body">
				<div id="guestbook-form-tab">
					<form method="post" id="guestbook-form">
						<p>
							Note: messages cannot be more than 200 characters
							<input type="hidden" name="ownerId" value="441794" />
						</p>
						<div>
							<textarea cols="15" rows="5" name="message" id="guestbook-message"></textarea>
							<script type="text/javascript">
								bbcodeToolbar = new Control.TextArea.ToolBar.BBCode("guestbook-message");
								bbcodeToolbar.toolbar.toolbar.id = "bbcode_toolbar";
								var colors = {
									"red": ["#d80000", "Red"],
									"orange": ["#fe6301", "Orange"],
									"yellow": ["#ffce00", "Yellow"],
									"green": ["#6cc800", "Green"],
									"cyan": ["#00c6c4", "Cyan"],
									"blue": ["#0070d7", "Blue"],
									"gray": ["#828282", "Grey"],
									"black": ["#000000", "Black"]
								};
								bbcodeToolbar.addColorSelect("Colours", colors, true);
							</script>
						</div>

						<div class="guestbook-toolbar clearfix">
							<a href="#" class="new-button" id="guestbook-form-cancel"><b>Quitter</b><i></i></a>
							<a href="#" class="new-button" id="guestbook-form-preview"><b>Pr&eacute;voir</b><i></i></a>
						</div>
					</form>
				</div>
				<div id="guestbook-preview-tab">&nbsp;</div>
			</div>
		</div>
		<div class="cbb topdialog" id="guestbook-delete-dialog">
			<h2 class="title dialog-handle">Supprimer</h2>

			<a class="topdialog-exit" href="#" id="guestbook-delete-dialog-exit">X</a>
			<div class="topdialog-body" id="guestbook-delete-dialog-body">
				<form method="post" id="guestbook-delete-form">
					<input type="hidden" name="entryId" id="guestbook-delete-id" value="" />
					<p>Es tu sur(e) de supprimer ce message?</p>
					<p>
						<a href="#" id="guestbook-delete-cancel" class="new-button"><b>Quitter</b><i></i></a>
						<a href="#" id="guestbook-delete" class="new-button"><b>Supprimer</b><i></i></a>
					</p>
				</form>
			</div>
		</div>
		<div id="group-tools" class="bottom-bubble">
			<div class="bottom-bubble-t">
				<div></div>
			</div>
			<div class="bottom-bubble-c">
				<h3>Edit group</h3>

				<ul>
					<li><a href="group_profile.php?id=<?php echo $groupid; ?>&do=edit" id="group-tools-style">Modifier la page</a></li>
					<?php if ($ownerid == $my_id) { ?><li><a href="#" id="group-tools-settings">Options</a></li><?php } ?>
					<li><a href="#" id="group-tools-badge">Badge</a></li>
					<li><a href="#" id="group-tools-members">Membres</a></li>
				</ul>

			</div>
			<div class="bottom-bubble-b">
				<div></div>
			</div>
		</div>

		<div class="cbb topdialog black" id="dialog-group-settings">

			<div class="box-tabs-container">
				<ul class="box-tabs">
					<li class="selected" id="group-settings-link-group"><a href="#">Group settings</a><span class="tab-spacer"></span></li>
					<li id="group-settings-link-forum"><a href="#">Forum Settings</a><span class="tab-spacer"></span></li>
					<li id="group-settings-link-room"><a href="#">Room Settings</a><span class="tab-spacer"></span></li>
				</ul>
			</div>

			<a class="topdialog-exit" href="#" id="dialog-group-settings-exit">X</a>
			<div class="topdialog-body" id="dialog-group-settings-body">
				<p style="text-align:center"><img src="./web-gallery/images/progress_bubbles.gif" alt="" width="29" height="6" /></p>
			</div>
		</div>

		<script language="JavaScript" type="text/javascript">
			Event.observe("dialog-group-settings-exit", "click", function(e) {
				Event.stop(e);
				closeGroupSettings();
			}, false);
		</script>
		<div class="cbb topdialog black" id="group-memberlist">

			<div class="box-tabs-container">
				<ul class="box-tabs">
					<li class="selected" id="group-memberlist-link-members"><a href="#">Membres</a><span class="tab-spacer"></span></li>
					<li id="group-memberlist-link-pending"><a href="#">Pending members</a><span class="tab-spacer"></span></li>
				</ul>
			</div>

			<a class="topdialog-exit" href="#" id="group-memberlist-exit">X</a>
			<div class="topdialog-body" id="group-memberlist-body">
				<div id="group-memberlist-members-search" class="clearfix" style="display:none">

					<a id="group-memberlist-members-search-button" href="#" class="new-button"><b>Chercher</b><i></i></a>
					<input type="text" id="group-memberlist-members-search-string" />
				</div>
				<div id="group-memberlist-members" style="clear: both"></div>
				<div id="group-memberlist-members-buttons" class="clearfix">
					<a href="#" class="new-button group-memberlist-button-disabled" id="group-memberlist-button-give-rights"><b>Donner des droits</b><i></i></a>
					<a href="#" class="new-button group-memberlist-button-disabled" id="group-memberlist-button-revoke-rights"><b>Enlever des droits</b><i></i></a>
					<a href="#" class="new-button group-memberlist-button-disabled" id="group-memberlist-button-remove"><b>Remove</b><i></i></a>
					<a href="#" class="new-button group-memberlist-button" id="group-memberlist-button-close"><b>Fermer</b><i></i></a>
				</div>
				<div id="group-memberlist-pending" style="clear: both"></div>
				<div id="group-memberlist-pending-buttons" class="clearfix">
					<a href="#" class="new-button group-memberlist-button-disabled" id="group-memberlist-button-accept"><b>Accepter</b><i></i></a>
					<a href="#" class="new-button group-memberlist-button-disabled" id="group-memberlist-button-decline"><b>Rejecter</b><i></i></a>
					<a href="#" class="new-button group-memberlist-button" id="group-memberlist-button-close2"><b>Fermer</b><i></i></a>
				</div>
			</div>
		</div>
	<?php } ?>

	<script type="text/javascript">
		HabboView.run();
	</script>

	<?php echo $analytics; ?>
	</body>

</html>

<?php
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
} else {
	$pagename = "Page not found";
?>

	<div id="container">
		<div id="content" style="position: relative" class="clearfix">
			<div id="column1" class="column">
				<div class="habblet-container ">
					<div class="cbb clearfix red ">

						<h2 class="title">Page not found!
						</h2>
						<div id="notfound-content" class="box-content">
							<p class="error-text">Sorry, but the page you were looking for was not found.</p> <img id="error-image" src="./web-gallery/v2/images/error.gif" />
							<p class="error-text">Please use the 'Back' button to get back to where you started.</p>
							<p class="error-text"><b>Search for group</b></p>
							<?php if (isset($searchString)) {
								echo "<p class=\"error-text\">Sorry, but no results were found for <strong>'" . $searchString . "'.</strong></p>";
							} ?>
							<p class="error-text">
							<form method='post'>
								Group Name:<br />
								<input type='text' name='searchString' maxlength='25' size='25' value='<?php echo $_POST['searchString']; ?>'>
								<input type='submit' class='submit' value='Submit'>
							</form>
							</p>
						</div>


					</div>
				</div>
				<script type="text/javascript">
					if (!$(document.body).hasClassName('process-template')) {
						Rounder.init();
					}
				</script>

			</div>
			<div id="column2" class="column">
				<div class="habblet-container ">
					<div class="cbb clearfix green ">

						<h2 class="title">Were you looking for...
						</h2>
						<div id="notfound-looking-for" class="box-content">
							<p><b>A friend's group or personal page?</b><br />
								See if it is listed on the <a href="community.php">Community</a> page.</p>

							<p><b>Rooms that rock?</b><br />
								Browse the <a href="community.php">Recommended Rooms</a> list.</p>

							<p><b>What other users are in to?</b><br />
								Check out the <a href="tags.php">Top Tags</a> list.</p>

							<p><b>How to get Credits?</b><br />
								Have a look at the <a href="credits.php">Credits</a> page.</p>
						</div>


					</div>
				</div>
				<script type="text/javascript">
					if (!$(document.body).hasClassName('process-template')) {
						Rounder.init();
					}
				</script>

			</div>

		<?php
		include('template/footer.php');
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	}
		?>