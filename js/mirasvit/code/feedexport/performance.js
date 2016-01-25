//---------------------- Variable Definitions -----------------------//
var url          = document.URL;
var queryParams = [];

// extract params
var position = url.indexOf('?');

if (position != -1) {
    var query_string = url.substring(position + 1, url.length)
    var pairs        = query_string.split('&');

    for(var i = 0; i < pairs.length; i++) {
        var vals = pairs[i].split('=');
        queryParams[vals[0]] = vals[1];
    }
}

var feedId       = queryParams['fee'] ? queryParams['fee'] : 0;
var product      = queryParams['fep'] ? queryParams['fep'] : 0;
var cookieDomain = document.domain;

//---------------------- Cookie Definitions -----------------------//
var Cookie = {
        getCookie : function(cookieName) {
                var dc = document.cookie;
                var prefix = escape(cookieName) + "=";
                var begin = dc.indexOf("; " + prefix);
                if (begin == -1) {
                        begin = dc.indexOf(prefix);
                        if (begin != 0) return null;
                } else {
                        begin += 2;
                }
                var end = document.cookie.indexOf(";", begin);
                if (end == -1) {
                        end = dc.length;
                }
                return unescape(dc.substring(begin + prefix.length, end));
        },

        setCookie : function(cookieName,cookieValue,nDays,path,domain) {
                var today = new Date();
                var expire = new Date();
                var string = escape(cookieName) + "=" + escape(cookieValue)
                if (nDays==null || nDays==0) {
                        // do nothing
                }else{
                        expire.setTime(today.getTime() + 3600000*24*nDays);
                        string += ";expires="+expire.toGMTString();
                }
                if (path) {
                        string += ";path="+path;
                }else{
                        string += ";path=/";
                }
                if (domain) {
                    document.cookie = string + ";domain="+domain;
                }
        }
};

//---------------------- End of Cookie Definitions -----------------------//

function feedExportTrackIt () {
    var currentDate = new Date();
    var cookieName  = "feedexport";
    var session     = Cookie.getCookie(cookieName);

    if (!session) {
        session  = '' + Math.floor(currentDate.getTime() / 1000) + Math.floor(Math.random() * 100000000000001);
    }

    if (session && feedId > 0 && product > 0) {
        var ndays = 730;
        var path = '/';
        Cookie.setCookie(cookieName, session, ndays, path, cookieDomain);
        Cookie.setCookie(cookieName + '_fee', feedId, ndays, path, cookieDomain);

        //setup image request
        var img_src = FEED_BASE_URL + 'feedexportfront/performance/click?rnd=' + Math.floor(Math.random() * 1000000000000001) + "&feed=" + feedId + "&session=" + session + "&product=" + product;
        console.log(img_src);
        // load the image
        var rep_img = new Image(1, 1);
        rep_img.src = img_src;
    }
}

feedExportTrackIt();