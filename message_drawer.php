<script type="application/javascript">
    function showAlert($id) {
        const thisAlert = document.getElementById("alert"+$id);

        thisAlert.style.transform = "scaleX(0.50) scaleY(0.75)"; // reset scale (effects case if message has been shown before)
        thisAlert.setAttribute("aria-hidden", "false");
        const links = thisAlert.querySelectorAll("a");
        links.forEach(function(link){
            link.tabIndex = 1;
        });


// Show the alert element
        thisAlert.style.display = "inline-block";

// Get the dimensions of the alert element
        var divWidth = thisAlert.offsetWidth;
        var divHeight = thisAlert.offsetHeight;

// Get the dimensions of the browser window
        var windowWidth = window.innerWidth;
        var windowHeight = window.innerHeight;

// Calculate the horizontal and vertical center of the browser window
        var centerX = windowWidth / 2;
        var centerY = windowHeight / 2;

// Calculate the left and top position of the alert element
        var leftPosition = centerX - (divWidth / 2);
        var topPosition = centerY - (divHeight / 2);

// Set the position of the alert element
        thisAlert.style.left = leftPosition + "px";
        thisAlert.style.top = topPosition + "px";

// Add event listener to detect screen size changes
        window.addEventListener("resize", function() {
            positionAlert();
        });

        function positionAlert() {
            // Get the dimensions of the alert element
            var divWidth = thisAlert.offsetWidth;
            var divHeight = thisAlert.offsetHeight;

            // Get the dimensions of the browser window
            var windowWidth = window.innerWidth;
            var windowHeight = window.innerHeight;

            // Calculate the horizontal and vertical center of the browser window
            var centerX = windowWidth / 2;
            var centerY = windowHeight / 2;

            // Calculate the left and top position of the alert element
            var leftPosition = centerX - (divWidth / 2);
            var topPosition = centerY - (divHeight / 2);

            // Set the position of the alert element
            thisAlert.style.left = leftPosition + "px";
            thisAlert.style.top = topPosition + "px";
        }

        setTimeout(function () {
            positionAlert();
            thisAlert.style.opacity = "1";
            thisAlert.style.transform = "scaleX(1) scaleY(1)";
            thisAlert.focus();
        }, 500);

// Get all elements with tabindex=1
        var tabindex1Elements = document.querySelectorAll('[tabindex="1"]');

// Set focus on the first tabindex=1 element
        tabindex1Elements[0].focus();
        return 1;
    }

    function hideAlert($id) {
        const thisAlert = document.getElementById("alert"+$id);
        thisAlert.style.opacity = "0";
        thisAlert.style.transform = "scaleX(1.25) scaleY(1.1)";
        setTimeout(function () {
            thisAlert.style.display = "none";
            thisAlert.setAttribute("aria-hidden", "true");
            const links = thisAlert.querySelectorAll("a");
            links.forEach(function(link){
                link.tabIndex = -1;
            });

        }, 500);

    }
</script>
<?php
function alert($title, $msg, $type = 'info', $visible = true, $yesORoklinkORdump = '', $noORlinkdump = '', $errType = '') {
    if ($title !== "Please wait" && $msg !== "Please wait for the language to load. If it's the first time the server handles this language's data, it could take a while...") {
        $title = translate($title, "de", $GLOBALS["lang"]);
        $msg = translate($msg, "de", $GLOBALS["lang"]);
    }
    //alert($title, $msg, $type = 'info', $visible = true, $yesORoklinkORdump = '', $noORlinkdump = '', $errType = '')

    $scriptStart = "<script type=\"application/javascript\">showAlert(\"";
    $scriptEnd = "</script>";
    if ($type != "info" && $type != "warning" && $type != "error") return "";
    $GLOBALS['alertid'] = $GLOBALS['alertid']+1;

    switch ($type) {
        case 'info':
            ?>
            <div  class="alert <?php echo $type; ?>"  aria-hidden="true" id="alert<?php echo $GLOBALS['alertid']; ?>" style="display: none; opacity: 0; transform: scaleX(0.50) scaleY(0.75);">
                <h2 style="outline-style: double; padding-left: 0.5em"><?php echo translate("Information", "de", $GLOBALS["lang"]); ?></h2>
                <ul class="actions stacked">
                    <li>
                        <h3><?php echo $title; ?></h3>
                        <div style="text-align: center;">
                            <b>
                                <?php echo $msg; ?>
                            </b>
                        </div><br>
                        <a tabindex='-1' class='button large fit' "
                        <?php
                        echo "onclick='hideAlert(" . $GLOBALS["alertid"] . "); ";
                        if ($yesORoklinkORdump !== "") echo " setTimeout(function() { window.location.href = \"" . $yesORoklinkORdump . "\"; }, 500);";
                        echo "'\">OK</a>";
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
        case 'warning':
            ?>
            <div  class="alert <?php echo $type; ?>" aria-hidden="true" id="alert<?php echo $GLOBALS['alertid']; ?>" style="display: none; opacity: 0; transform: scaleX(0.50) scaleY(0.75);">
                <h2 style="outline-style: double; padding-left: 0.5em"><?php echo translate("Warnung", "de", $GLOBALS["lang"]); ?></h2>
                <ul class="actions stacked">
                    <li>
                        <h3><?php echo $title; ?></h3>
                        <div style="text-align: center;">
                            <b><?php echo $msg; ?>
                            </b>
                        </div>
                        <br>
                        <?php
                        if ($yesORoklinkORdump == "" || $noORlinkdump == "") {
                            ?>
                            <a tabindex='-1' class='button large fit' onclick='hideAlert("<?php echo $GLOBALS['alertid']; ?>")'>OK</a>
                            <?php
                        }
                        else {
                            ?>
                            <a tabindex='-1' class='button large fit' onclick='<?php echo $yesORoklinkORdump; ?>'><?php echo translate("Ja", "de", $GLOBALS["lang"]); ?></a>

                            <?php
                            if ($noORlinkdump == "close") {
                                ?>
                                <a tabindex='-1' class='button large fit' onclick='hideAlert("<?php echo $GLOBALS['alertid']; ?>")'><?php echo translate("Nein", "de", $GLOBALS["lang"]); ?></a>
                                <?php
                            } else {
                                ?>
                                <a tabindex='-1' class='button large fit' onclick='<?php echo $noORlinkdump; ?>'><?php echo translate("Nein", "de", $GLOBALS["lang"]); ?></a>
                                <?php
                            }
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
            <div  class="alert <?php echo $type; ?>" aria-hidden="true" id="alert<?php echo $GLOBALS['alertid']; ?>" style="display: none; opacity: 0; transform: scaleX(0.50) scaleY(0.75);">
                <h2 style="outline-style: double; padding-left: 0.5em"><?php echo translate("Fehler", "de", $GLOBALS["lang"]); ?></h2>
                <ul class="actions stacked">
                    <li>
                        <h3><?php echo $title; ?></h3>
                        <div style="text-align: center;">
                            <b>
                                <?php
                                echo $msg;
                                if ($yesORoklinkORdump != "" || $noORlinkdump != "") {
                                    if ($noORlinkdump != "") $noORlinkdump = " link-dump: " . $noORlinkdump;
                                    echo "<br><br>".translate("Das tut uns leid, bitte sende uns folgende E-Mail. <br>Sie enthält bereits alle Infos, die wir brauchen, Du musst nichts hinzufügen.<br><br>Danke für Deine Unterstützung.", "de", $GLOBALS["lang"])."<br>
                                    <br> <a tabindex='-1' href='mailto:kontakt@eh-umfragen.de?subject=" . $yesORoklinkORdump . $noORlinkdump . "'>>>".translate("E-Mail senden", "de", $GLOBALS["lang"])."<<</a>";
                                }
                                ?>
                            </b>
                        </div>
                        <br>
                        <a tabindex='-1' class='button large fit' onclick='hideAlert("<?php echo $GLOBALS['alertid']; ?>")'>OK</a>
                        <a tabindex="-1" style="opacity: 0; height: 0; width: 0"></a> <!-- in order to bind the users tabbing and the difficulty of selecting the last value -->
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
    } ?>

    <script type="application/javascript">
        var alertElements = document.getElementsByClassName("alert");
        var currentAlert;
        for (var i = 0; i < alertElements.length; i++) {

            // create the top div element
            const alertTop = document.createElement("div");
            //append the new element to the alert window

            const thisAlert = alertElements[i];
            thisAlert.appendChild(alertTop); // append to the alert element

            alertTop.style.height = "40px";
            alertTop.style.backgroundColor = "rgba(0, 0, 0, 0.01)";
            alertTop.style.position = "absolute"; // change to absolute
            alertTop.style.top = "0";
            alertTop.style.left = "0";
            alertTop.style.right = "0";

            // add event listener to the whole alert div element
            thisAlert.addEventListener("mousedown", setZIndex);
            thisAlert.addEventListener("touchstart", setZIndex);

            function setZIndex() {

                // Set the z-index of the clicked alert element to a higher value
                this.style.zIndex = "10005";

                // Lower the z-index of all other alert elements
                for (var j = 0; j < alertElements.length; j++) {
                    if (alertElements[j] !== this) {
                        alertElements[j].style.zIndex = "10004";
                    }
                }
            }

            // add event listener to the top div element
            alertTop.addEventListener("mousedown", setAlertPositionMouse);
            alertTop.addEventListener("touchstart", setAlertPositionTouch);

            function setAlertPositionMouse(e) {
                e.preventDefault();
                currentAlert = this.parentElement;
                var initialX = e.clientX;
                var initialY = e.clientY;
                var alertTop = currentAlert.offsetTop;
                var alertLeft = currentAlert.offsetLeft;


                document.addEventListener("mousemove", mouseMove);
                document.addEventListener("mouseup", mouseUp);

                function mouseMove(e) {
                    e.preventDefault();
                    currentAlert.style.top = alertTop + (e.clientY - initialY) + "px";
                    currentAlert.style.left = alertLeft + (e.clientX - initialX) + "px";
                }

                function mouseUp() {
                    document.removeEventListener("mousemove", mouseMove);
                    document.removeEventListener("mouseup", mouseUp);
                }
            }
            function setAlertPositionTouch(e) {
                e.preventDefault();
                currentAlert = this.parentElement;
                var initialX = e.touches[0].clientX;
                var initialY = e.touches[0].clientY;
                var alertTop = currentAlert.offsetTop;
                var alertLeft = currentAlert.offsetLeft;

                document.addEventListener("touchmove", touchMove);
                document.addEventListener("touchend", touchEnd);

                function touchMove(e) {
                    e.preventDefault();
                    currentAlert.style.top = alertTop + (e.touches[0].clientY - initialY) + "px";
                    currentAlert.style.left = alertLeft + (e.touches[0].clientX - initialX) + "px";
                }

                function touchEnd() {
                    document.removeEventListener("touchmove", touchMove);
                    document.removeEventListener("touchend", touchEnd);
                }
            }
        }
/** TODO <a> not working*/
        document.addEventListener("keydown", function(event) {
            if (event.code === "Enter" || event.code === "Space") {
                event.preventDefault();
                var focusedElement = document.activeElement;
                if (focusedElement.classList.contains("button")) {
                    focusedElement.click();
                } else if (focusedElement.tagName === "A" && focusedElement.href) {
                    window.location.href = focusedElement.href;
                }
            }
        });

    </script>



                <?php
    return $GLOBALS['alertid'];
}
/*
alert("titel", "msg", "info");
alert("titel", "msg", "warning");
alert("titel", "msg", "error");
*/
?>