<script type="application/javascript">
    function showAlert($id) {
        const thisAlert = document.getElementById("alert"+$id);

        thisAlert.style.display = "inline-block";
        var divHeight = thisAlert.offsetHeight;
        var topDistance = (window.innerHeight - divHeight) / 2;
        thisAlert.style.top = topDistance + "px";

        setTimeout(async () => {
            thisAlert.style.opacity = "1";
            thisAlert.style.transform = "scaleX(1) scaleY(1)";
        }, 500);
    }

    function hideAlert($id) {
        const thisAlert = document.getElementById("alert"+$id);
        thisAlert.style.opacity = "0";
        thisAlert.style.transform = "scaleX(1.25) scaleY(1.1)";
        setTimeout(async () => {
            thisAlert.style.display = "none";
        }, 500);

    }
</script>
<?php
function alert($title, $msg, $type = 'info', $visible = true, $yesORoklinkORdump = '', $noORlinkdump = '', $errType = '') {
    $scriptStart = "<script type=\"application/javascript\">showAlert(\"";
    $scriptEnd = "</script>";
    if ($type != "info" && $type != "warning" && $type != "error") return;
    $GLOBALS['alertid'] = $GLOBALS['alertid']+1;

    switch ($type) {
        case 'info':
            ?>
            <div  class="<?php echo $type; ?>" id="alert<?php echo $GLOBALS['alertid']; ?>" style="display: none; opacity: 0; transform: scaleX(0.75) scaleY(0.94);">
                <h2 style="outline-style: double; padding-left: 0.5em">Information</h2>
                <ul class="actions stacked">
                    <li>
                        <h3><?php echo $title; ?></h3>
                        <p>
                        <center>
                            <b><?php echo $msg; ?>
                            </b>
                        </center>
                        </p><br>
                        <a class='button large fit' "
                        <?php
                        echo "onclick='hideAlert(" . $GLOBALS["alertid"] . "); ";
                        if ($yesORoklinkORdump !== "") echo " setTimeout(function() { window.location.href = \"" . $yesORoklinkORdump . "\"; }, 500);";
                        echo "'";
                        ?>
                        ">OK</a>
                    </li>
                </ul>
            </div>
            <?php
            if ($visible) {
                ?>
                <script type="application/javascript" style="opacity: 0; height: 0; width: 0">
                    showAlert("<?php echo $GLOBALS['alertid']; ?>");
                </script>
                <?php
            }
            break;
        case 'warning':
            ?>
            <div  class="<?php echo $type; ?>" id="alert<?php echo $GLOBALS['alertid']; ?>" style="display: none; opacity: 0; transform: scaleX(0.75) scaleY(0.94);">
                <h2 style="outline-style: double; padding-left: 0.5em">Warnung</h2>
                <ul class="actions stacked">
                    <li>
                        <h3><?php echo $title; ?></h3>
                        <p>
                        <center>
                            <b><?php echo $msg; ?>
                            </b>
                        </center>
                        </p><br>
                        <?php
                        if ($yesORoklinkORdump == "" || $noORlinkdump == "") {
                        ?>
                        <a class='button large fit' onclick='hideAlert("<?php echo $GLOBALS['alertid']; ?>")'>OK</a>
                        <?php
                        }
                        else {
                        ?>
                            <a class='button large fit' onclick='<?php echo $yesORoklinkORdump; ?>'>Ja</a>
                            <a class='button large fit' onclick='<?php echo $noORlinkdump; ?>'>Nein</a>
                            <?php
                        }
                        ?>
                    </li>
                </ul>
            </div>
            <?php
            if ($visible) {
                ?>
                <script type="application/javascript" style="opacity: 0; height: 0; width: 0">
                    showAlert("<?php echo $GLOBALS['alertid']; ?>");
                </script>
                <?php
            }
            break;
        case 'error':
            ?>
            <div  class="<?php echo $type; ?>" id="alert<?php echo $GLOBALS['alertid']; ?>" style="display: none; opacity: 0; transform: scaleX(0.75) scaleY(0.94);">
                <h2 style="outline-style: double; padding-left: 0.5em">Fehler</h2>
                <ul class="actions stacked">
                    <li>
                        <h3><?php echo $title; ?></h3>
                        <p>
                        <center>
                            <b>
                                <?php
                                echo $msg;
                                if ($yesORoklinkORdump != "" || $noORlinkdump != "") {
                                    if ($noORlinkdump != "") $noORlinkdump = " link-dump: " . $noORlinkdump;
                                    echo "<br><br>Das tut uns leid, bitte sende uns folgende E-Mail. <br>Sie enthält bereits alle Infos, die wir brauchen, Du musst nichts hinzufügen.<br><br>Danke für Deine Unterstützung.<br>
                                    <br> <a href='mailto:kontakt@eh-umfragen.de?subject=" . $yesORoklinkORdump . $noORlinkdump . "'>>>E-Mail senden<<</a>";
                                }
                                ?>
                            </b>
                        </center>
                        </p><br>
                        <a class='button large fit' onclick='hideAlert("<?php echo $GLOBALS['alertid']; ?>")'>OK</a>
                    </li>
                </ul>
            </div>
            <?php
            if ($visible) {
                ?>
                <script type="application/javascript" style="opacity: 0; height: 0; width: 0">
                    showAlert("<?php echo $GLOBALS['alertid']; ?>");
                </script>
                <?php
            }
            break;
    }
    return $GLOBALS['alertid'];
}
/*
alert("titel", "msg", "info");
alert("titel", "msg", "warning");
alert("titel", "msg", "error");
*/
?>