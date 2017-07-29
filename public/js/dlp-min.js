function extractUrlValue(key, url) {
    if (typeof(url) === 'undefined')
        url = window.location.href;
    var match = url.match('[?&]' + key + '=([^&#]+)');
    return match ? match[1] : null;
}

var exp_id = extractUrlValue('adlp', window.location.href);
if (exp_id !== null) {
    var params = "full_url="+window.location.href+"&hostname="+window.location.hostname+"&path="+window.location.pathname;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4) {
            if (this.status == 200) {
                var response = JSON.parse(this.responseText);
                var options = response.data;

                for (var i = 0; i < options.length; i++) {
                    var css_selector = options[i]['css_selector'];
                    var type = options[i]['type'];
                    var value = options[i]['value'];
                    var obj = document.querySelector(css_selector);
                    
                    if (obj != null) {
                        if (type == 'text')
                            obj.innerHTML = value;
                        else if (type == 'image')
                            obj.src = value;
                    }
                }
            } else {
                console.log(this.response);
            }
        }
    };
    //xhttp.open("POST", "http://dev.local.dlp/api/getExpInfo", true);
    //xhttp.open("POST", "https://adloha-dlp.dev/api/getExpInfo", true);
    xhttp.open("POST", "https://dlp-app.herokuapp.com/api/getExpInfo", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(params);
}
