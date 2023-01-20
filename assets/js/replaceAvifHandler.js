window.addEventListener('load', function() {
    if (!isAvifSupported()) {
        // Get all the style sheets on the page
        var styleSheets = document.styleSheets;
        for (var i = 0; i < styleSheets.length; i++) {
            var styleSheet = styleSheets[i];
            // Get all the rules in the style sheet
            var rules = styleSheet.rules || styleSheet.cssRules;
            for (var j = 0; j < rules.length; j++) {
                var rule = rules[j];
                // Check if the rule has a `background-image` property
                if (rule.style && rule.style.backgroundImage) {
                    // Check if the `background-image` value is an AVIF image
                    var avifRegex = /url\("(.+\.avif)"\)/;
                    var match = avifRegex.exec(rule.style.backgroundImage);
                    if (match) {
                        // Get the name of the AVIF image
                        var avifName = match[1];
                        // Check if there is an alternative image with a different extension
                        var alternativeNameJpg = avifName.replace(/\.avif$/, '.jpg');
                        var alternativeNameJpeg = avifName.replace(/\.avif$/, '.jpeg');
                        var alternativeNamePng = avifName.replace(/\.avif$/, '.png');
                        if (doesFileExist(alternativeNameJpg)) {
                            // Replace the AVIF image with the alternative image
                            rule.style.backgroundImage = 'url("' + alternativeNameJpg + '")';
                        } else if (doesFileExist(alternativeNameJpeg)) {
                            // Replace the AVIF image with the alternative image
                            rule.style.backgroundImage = 'url("' + alternativeNameJpeg + '")';
                        } else if (doesFileExist(alternativeNamePng)) {
                            // Replace the AVIF image with the alternative image
                            rule.style.backgroundImage = 'url("' + alternativeNamePng + '")';
                        }
                    }
                }
            }
        }
    }
});

function doesFileExist(url) {
    // This function can be used to check if a file exists by making a HEAD request to the file URL
    // Return true if the file exists, false if it doesn't
    var xhr = new XMLHttpRequest();
    xhr.open('HEAD', url, false);
    xhr.send();
    return xhr.status !== 404;
}