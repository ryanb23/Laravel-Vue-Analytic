function extractUrlValue(t,e){void 0===e&&(e=window.location.href);var a=e.match("[?&]"+t+"=([^&#]+)");return a?a[1]:null}var exp_id=extractUrlValue("adlp",window.location.href);if(null!==exp_id){var params="full_url="+window.location.href+"&hostname="+window.location.hostname+"&path="+window.location.pathname,xhttp=new XMLHttpRequest;xhttp.onreadystatechange=function(){if(4==this.readyState&&200==this.status)for(var t=JSON.parse(this.responseText),e=t.data,a=0;a<e.length;a++){var n=e[a].css_selector,o=e[a].type,r=e[a].value,p=document.querySelector(n);null!=p&&("text"==o?p.innerHTML=r:"image"==o&&(p.src=r))}},xhttp.open("POST","https://dlp-app.herokuapp.com/api/getExpInfo",!0),xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded"),xhttp.send(params)}