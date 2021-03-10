'use strict';

const d = document;

// Envía solicitud al servidor
function sendHttpRequest(method, url, data, callback) {
    const xhr = getXhr();
    function getXhr() {
        if (window.XMLHttpRequest) {
            return new XMLHttpRequest();
        } else {
            return new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
    xhr.onreadystatechange = function (e) {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status == 200 && xhr.response != null) {
                callback(xhr.response);
            } else {
                console.log("There was a problem retrieving the data: " + xhr.statusText);
            }
        }
    }
    xhr.open(method, url + ((/\?/).test(url) ? "&" : "?") + (new Date()).getTime());
    xhr.onloadstart = function (e) {
    }
    xhr.onloadend = function (e) {
    }
    if (data && !(data instanceof FormData)) xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send(data);
    xhr.onerror = function (e) {
        console.log("Error: " + e + " Could not load url.");
    }
}

function isEmpty(str) {
    return (str.length == 0);
}

function setError(el, msg) {
    el.classList.add("invalid");
    el.parentNode.querySelector('.form-error').innerHTML = msg;
}

function setSuccess(el) {
    el.classList.remove("invalid");
    el.parentNode.querySelector('.form-error').innerHTML = "";
}

function validEmail(email) {
    // https://owasp.org/www-community/OWASP_Validation_Regex_Repository
    const regexEmail = /^[a-zA-Z0-9_+&*-]+(?:\.[a-zA-Z0-9_+&*-]+)*@(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,7}$/;
    return regexEmail.test(email);
}