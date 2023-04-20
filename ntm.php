<?PHP
#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|
#|                                                                        #|
#|         Copyright © 2014-2023 - MyHabbo Tout droits réservés.          #|
#|																		  #|
#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|#|

include("./config.php");
$pagename = "Hotel";
$pageid = "Hotel";

if (!isset($_SESSION['username'])) {
    Redirect("" . $url . "/index");
} else {
    session_destroy();
}

$ssoTicket = "HABBO-" . GenerateRandom("sso");
$updateSSO = $bdd->prepare("UPDATE users SET auth_ticket = ? WHERE id = ?");
$updateSSO->execute([$ssoTicket, $user['id']]);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />

    <title><?PHP echo $sitename; ?>: <?PHP echo $pagename; ?></title>

    <script type="text/javascript">
        var andSoItBegins = (new Date()).getTime();
        var ad_keywords = "";
        document.habboLoggedIn = true;
        var habboName = "<?PHP echo $user['username']; ?>";
        var habboReqPath = "<?PHP echo $url; ?>";
        var habboStaticFilePath = "<?PHP echo $imagepath; ?>";
        var habboImagerUrl = "http://www.habbo.com/habbo-imaging/";
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

    <script src="<?PHP echo $imagepath; ?>static/js/fullcontent.js" type="text/javascript"></script>
    <link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/style.css" type="text/css" />
    <link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/buttons.css" type="text/css" />
    <link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/boxes.css" type="text/css" />
    <link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/tooltips.css" type="text/css" />
    <link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/habboclient.css" type="text/css" />
    <link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/habboflashclient.css" type="text/css" />
    <script src="<?PHP echo $imagepath; ?>static/js/habboflashclient.js" type="text/javascript"></script>

    <meta name="description" content="<?PHP echo $description; ?>" />
    <meta name="keywords" content="<?PHP echo $keyword; ?>" />

    <!--[if IE 8]>
<link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/ie8.css" type="text/css" />
<![endif]-->
    <!--[if lt IE 8]>
<link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/ie.css" type="text/css" />
<![endif]-->
    <!--[if lt IE 7]>
<link rel="stylesheet" href="<?PHP echo $imagepath; ?>v2/styles/ie6.css" type="text/css" />
<script src="<?PHP echo $imagepath; ?>static/js/pngfix.js" type="text/javascript"></script>
<script type="text/javascript">
try { document.execCommand('BackgroundImageCache', false, true); } catch(e) {}
</script>
 
<style type="text/css">
body { behavior: url(http://www.habbo.co.uk/js/csshover.htc); }
</style>
<![endif]-->
    <meta name="build" content="<?PHP echo $name; ?> >> <?PHP echo $build; ?>" />

</head>

<body id="client" class="flashclient">

    <iframe src="<?= ClientNitro() . $ssoTicket ?>" width="100%" height="100%" frameborder="0" scrolling="no"></iframe>
    <script type="text/javascript">
        $('content').show();
    </script>

    <div id="overlay"></div>
    <div id="client-ui">
        <div id="flash-wrapper">
            <div id="flash-container">
                <div id="content" style="width: 400px; margin: 20px auto 0 auto; display: none">
                    <div class="cbb clearfix">
                        <h2 class="title">Installer Adode Flash Player</h2>
                        <div class="box-content">
                            <p>Pour installer Flash Player : <a href="http://get.adobe.com/flashplayer/">Clique ICI</a>. More instructions for installation can be found here: <a href="http://www.adobe.com/products/flashplayer/productinfo/instructions/">More information</a></p>

                            <p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://images.habbo.com/habboweb/45_0061af58e257a7c6b931c91f771b4483/2/web-gallery/images/client/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
                        </div>
                    </div>
                </div>
                <script type="text/javascript">
                    $('content').show();
                </script>
                <noscript>
                    <div style="width: 400px; margin: 20px auto 0 auto; text-align: center">
                        <p>If you are not automatically redirected, please <a href="/client/nojs">click here</a></p>
                    </div>
                </noscript>
            </div>
        </div>
        <div id="content" class="client-content"></div>
    </div>
    <div style="display: none">

        <script language="JavaScript" type="text/javascript">
            setTimeout(function() {
                HabboCounter.init(600);
            }, 20000);
        </script>
    </div>
    <script type="text/javascript">
        RightClick.init("flash-wrapper", "flash-container");
    </script>


</body>

</html>